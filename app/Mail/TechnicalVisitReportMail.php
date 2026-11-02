<?php

namespace App\Mail;

use App\Models\TechnicalVisits;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** Compte rendu de visite technique envoyé au client après signature sur l'app mobile. */
class TechnicalVisitReportMail extends Mailable
{
    use Queueable, SerializesModels;

    /** Libellés des contrôles (mêmes que l'app mobile / le back-office). */
    public const CONTROLS = [
        'control_1'  => "Niveau d'huile moteur",
        'control_2'  => 'Niveau du liquide de refroidissement',
        'control_3'  => "Niveau de l'électrolyte batterie",
        'control_4'  => 'Filtre à huile',
        'control_5'  => 'Filtre à air',
        'control_6'  => 'Filtre à carburant',
        'control_7'  => 'Circuit carburant',
        'control_8'  => 'Circuit de refroidissement',
        'control_9'  => 'État / présence des courroies',
        'control_10' => 'Chargeur batterie',
        'control_11' => 'Résistance chauffante',
        'control_12' => 'Démarrage du GE',
        'control_13' => 'Fonctionnement du démarreur',
        'control_14' => 'État du GE et du local',
        'control_15' => "État de l'arrêt d'urgence",
    ];

    public function __construct(public TechnicalVisits $visit) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), 'Sporafric Motors — Service technique'),
            subject: 'Compte rendu de visite technique — ' . ($this->visit->generator?->name ?? 'Groupe électrogène') . ' — ' . $this->visit->date?->format('d/m/Y'),
        );
    }

    public function content(): Content
    {
        $v = $this->visit;
        $generator = $v->generator;
        $site = $generator?->contractGenerator?->site ?? $generator?->devisGenerator?->site;
        $triplet = fn (?array $values, array $keys, string $unit) => $values
            ? collect($keys)->map(fn ($label, $k) => isset($values[$k]) && $values[$k] !== '' ? "{$label} {$values[$k]} {$unit}" : null)->filter()->implode(' · ') ?: null
            : null;

        return new Content(view: 'emails.intervention-report', with: [
            'title'     => 'Compte rendu de visite technique',
            'reference' => $v->date?->format('d/m/Y'),
            'rows'      => array_filter([
                'Client'      => $v->devis?->customer?->name ?? $v->contract?->customer?->name ?? $generator?->contractGenerator?->contract?->customer?->name,
                'Site'        => $site,
                'Groupe'      => trim(($generator?->name ?? '') . ($generator?->reference ? " ({$generator->reference})" : '')) ?: null,
                'Date'        => $v->date?->format('d/m/Y'),
                'Technicien'  => $v->user?->name,
                'Batterie'    => match ((string) $v->control_battery) { '1' => 'Densité 1,26 à 1,28', '2' => 'Densité 1,22 à 1,26', '3' => 'Densité < 1,22', default => null },
                'Charge moteur' => $v->control_circuit !== null ? "{$v->control_circuit} V" : null,
                'Fréquence'   => $v->control_frequence !== null ? "{$v->control_frequence} Hz" : null,
                'Tension 230 V' => $triplet($v->control_tension, ['v1' => 'V1n', 'v2' => 'V2n', 'v3' => 'V3n'], 'V'),
                'Tension 400 V' => $triplet($v->control_tension_2, ['u1' => 'U12', 'u2' => 'U13', 'u3' => 'U23'], 'V'),
                'Intensité'   => $triplet($v->control_intensite, ['i1' => 'I1', 'i2' => 'I2', 'i3' => 'I3'], 'A'),
                'Mode'        => match ((string) $v->control_16) { '1' => 'Manuel', '0' => 'Automatique', default => null },
            ]),
            'checklist' => collect(self::CONTROLS)->map(fn ($label, $key) => ['label' => $label, 'ok' => (bool) $v->{$key}])->values()->all(),
            'sections'  => array_filter(['Observations' => $v->notes]),
            'model'     => $v,
        ]);
    }
}
