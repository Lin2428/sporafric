<?php

namespace App\Services;

use App\Mail\InterventionReportMail;
use App\Mail\TechnicalVisitReportMail;
use App\Models\ContractGenerator;
use App\Models\DevisGenerator;
use App\Models\Intervention;
use App\Models\TechnicalVisits;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * Signature du client (app mobile) + email de compte rendu d'intervention / de visite technique.
 *
 * L'email part une seule fois (report_sent_at), APRÈS la réponse HTTP (pas de worker de queue
 * requis, la synchro mobile n'attend pas le SMTP). Destinataire : email saisi / confirmé par le
 * technicien, sinon contact du site (contrat / devis), sinon contacts du client.
 */
class ClientReportService
{
    /** Règles de validation communes des champs signature envoyés par l'app. */
    public static function rules(): array
    {
        return [
            'signature'         => ['nullable', 'string'],          // data URL PNG
            'signed_by'         => ['nullable', 'string', 'max:255'],
            'signed_at'         => ['nullable', 'date'],
            'signature_refusal' => ['nullable', 'string', 'max:255'], // client absent / refus
            'client_email'      => ['nullable', 'email', 'max:255'],
        ];
    }

    /**
     * Applique les champs signature validés sur le modèle (sans sauvegarder).
     * Ne remplace pas une signature déjà enregistrée (renvoi de synchro).
     */
    public static function fill(Intervention|TechnicalVisits $model, array $validated): void
    {
        if (! empty($validated['signature']) && ! $model->signature_path) {
            $model->signature_path = static::storeSignature($validated['signature']);
        }
        foreach (['signed_by', 'signature_refusal', 'client_email'] as $field) {
            if (! empty($validated[$field])) {
                $model->{$field} = $validated[$field];
            }
        }
        if (($model->signature_path || $model->signature_refusal) && ! $model->signed_at) {
            $model->signed_at = isset($validated['signed_at'])
                ? \Illuminate\Support\Carbon::parse($validated['signed_at'])->setTimezone(config('app.timezone'))
                : now();
        }
    }

    public static function storeSignature(string $dataUrl): ?string
    {
        if (! preg_match('#^data:image/(png|jpe?g);base64,(.+)$#', $dataUrl, $m)) {
            return null;
        }
        $binary = base64_decode($m[2], true);
        if ($binary === false || strlen($binary) > 2 * 1024 * 1024) {
            return null;
        }
        $path = 'signatures/' . Str::uuid() . '.' . ($m[1] === 'png' ? 'png' : 'jpg');
        Storage::disk('public')->put($path, $binary);

        return $path;
    }

    /** Email du client : saisi sur le terrain, sinon contact du site, sinon fiche client. */
    public static function recipient(Intervention|TechnicalVisits $model): ?string
    {
        $candidates = [
            $model->client_email,
            static::siteLink($model)?->contact_email,
        ];
        $customer = $model instanceof Intervention
            ? ($model->devis?->customer ?? $model->contract?->customer ?? $model->customer)
            : ($model->devis?->customer ?? $model->contract?->customer);
        $candidates[] = $customer?->contact_c_email;
        $candidates[] = $customer?->contact_l_email;

        foreach ($candidates as $email) {
            $email = trim((string) $email);
            if ($email !== '' && Validator::make(['e' => $email], ['e' => 'email'])->passes()) {
                return $email;
            }
        }

        return null;
    }

    /**
     * Mémorise l'email saisi sur le terrain comme contact du site s'il n'en avait pas
     * (pré-rempli aux prochaines interventions).
     */
    public static function rememberSiteEmail(Intervention|TechnicalVisits $model): void
    {
        $link = static::siteLink($model);
        if ($link && $model->client_email && blank($link->contact_email)) {
            $link->timestamps = false;
            $link->forceFill(['contact_email' => $model->client_email])->saveQuietly();
        }
    }

    /** Envoi différé après la réponse HTTP ; une seule fois par intervention / visite. */
    public static function sendAfterResponse(Intervention|TechnicalVisits $model): void
    {
        if ($model->report_sent_at || (! $model->signature_path && ! $model->signature_refusal)) {
            return;
        }
        $class = $model::class;
        $id    = $model->getKey();

        dispatch(function () use ($class, $id) {
            $fresh = $class::query()->find($id);
            if ($fresh) {
                ClientReportService::send($fresh);
            }
        })->afterResponse();
    }

    public static function send(Intervention|TechnicalVisits $model): bool
    {
        if ($model->report_sent_at) {
            return true;
        }

        $to = static::recipient($model);
        if (! $to) {
            $model->forceFill(['report_error' => 'Aucun email client'])->saveQuietly();
            return false;
        }

        try {
            $mail = $model instanceof Intervention ? new InterventionReportMail($model) : new TechnicalVisitReportMail($model);
            Mail::to($to)->send($mail);
            $model->forceFill(['report_sent_at' => now(), 'report_error' => null, 'client_email' => $model->client_email ?: $to])->saveQuietly();

            return true;
        } catch (\Throwable $e) {
            Log::error('Compte rendu client non envoyé', ['model' => $model::class, 'id' => $model->getKey(), 'error' => $e->getMessage()]);
            $model->forceFill(['report_error' => Str::limit($e->getMessage(), 250)])->saveQuietly();

            return false;
        }
    }

    /** Ligne pivot du site (contact) : contrat ou devis du GE concerné. */
    private static function siteLink(Intervention|TechnicalVisits $model): ContractGenerator|DevisGenerator|null
    {
        if (! $model->generator_id) {
            return null;
        }
        if ($model->contract_id) {
            return ContractGenerator::query()->where('contract_id', $model->contract_id)->where('generator_id', $model->generator_id)->first();
        }
        if ($model->devis_id) {
            return DevisGenerator::query()->without(['devis', 'generator', 'oldGenerator'])
                ->where('devis_id', $model->devis_id)->where('generator_id', $model->generator_id)->first();
        }

        return null;
    }
}
