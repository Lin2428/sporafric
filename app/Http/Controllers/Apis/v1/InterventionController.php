<?php

namespace App\Http\Controllers\Apis\v1;

use App\Enum\InterventionStatus;
use App\Enum\InterventionType;
use App\Http\Controllers\Controller;
use App\Models\Intervention;
use App\Models\InterventionFiche;
use App\Services\ClientReportService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class InterventionController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $interventionsQuery = Intervention::query()
            ->whereHas("interventionTechniciens", function ($query) use ($user) {
                return $query->where("technicien_id", $user->technicien_id);
            })->whereDate("date_planifiee", "=", now());

        $data = (clone $interventionsQuery)
            ->without(['interventionTechniciens', 'pieces', 'newGenerator', 'fiches'])
            ->with(['customer:id,name', 'generator:id,name,reference,next_vidange'])
            ->whereNotNull('date_planifiee')
            ->whereDate('date_planifiee', now())
            ->where('cancelled', false)
            ->orderBy('date_planifiee')
            ->get()
            ->map(fn(Intervention $intervention) => [
                'id' => $intervention->id,
                'numero' => $intervention->numero,
                'date_planifiee' => $intervention->date_planifiee,
                'start_date' => $intervention->start_date,
                'end_date' => $intervention->end_date,
                'type' => $intervention->type,
                'type_label' => InterventionType::tryFrom((string)$intervention->type)?->label(),
                'status' => $intervention->status,
                'status_label' => InterventionStatus::tryFrom((string)$intervention->status)?->label(),
                'customer' => $intervention->devis?->customer?->name ?? $intervention->contract?->customer?->name ?? $intervention->customer?->name,
                'generator' => $intervention->generator?->name,
                'generator_houres' => $intervention->houres,
                'consta' => $intervention->description_panne,
                'travaux' => $intervention->travaux,
                'nex_vidange' => $intervention->generator?->next_vidange,
                'site' => $intervention->site ?? $intervention->contract?->generators?->first()?->pivot?->site ?? $intervention->devis?->generators?->first()?->pivot?->site,
                'phone' => $intervention->contract?->generators?->first()?->pivot?->contact_phone ?? $intervention->devis?->generators?->first()?->pivot?->contact_phone ?? $intervention->customer?->contact_c_phone,
                // Destinataire pré-rempli du compte rendu (modifiable au moment de la signature)
                'email' => ClientReportService::recipient($intervention),
                'signed' => (bool) ($intervention->signature_path || $intervention->signature_refusal),
                'images' => $intervention->fiches->map(function (InterventionFiche $fiche) {
                    $data = [];
                    if ($fiche->fiche != null && !str_contains($fiche->fiche, ".pdf")) {
                        $data = [
                            'id' => $fiche->id,
                            'intervention_id' => $fiche->intervention_id,
                            'url' => rtrim(config('app.url'), '/')."/storage/devis/".$fiche->fiche,
                        ];
                    }
                    return $data;
                }),
                'created_at' => $intervention->created_at,
                'updated_at' => $intervention->updated_at,
                'is_synced' => $intervention->is_synced,
            ]);

        return response()->json([
            "success" => true,
            "message" => "Interventions chargée avec succès",
            "data" => $data,
            ]);

    }

    public function complete(Request $request, int $id): JsonResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'rapport' => ['nullable', 'string'],
            'constat' => ['nullable', 'string'],
            'houres' => ['nullable', 'numeric', 'min:0'],
            'statut' => ['required', 'in:en_attente,en_cours,termine,annule'],
            'heure_debut' => ['nullable', 'date_format:H:i'],
            'heure_fin' => ['nullable', 'date_format:H:i'],
            ...ClientReportService::rules(),
        ]);

        $intervention = Intervention::query()
            ->whereKey($id)
            ->whereHas('interventionTechniciens', fn ($q) => $q->where('technicien_id', $user->technicien_id))
            ->first();

        if (!$intervention) {
            return response()->json([
                'success' => false,
                'message' => "Cette intervention ne vous est pas assignée.",
            ], 403);
        }

        $day = $intervention->date_planifiee ? Carbon::parse($intervention->date_planifiee)->format('Y-m-d') : now()->format('Y-m-d');

        $update = [
            'travaux' => $validated['rapport'] ?? $intervention->travaux,
            'cancelled' => $validated['statut'] === 'annule',
        ];

        // Constat et compteur saisis sur le terrain (repris dans le compte rendu client)
        if (array_key_exists('constat', $validated) && $validated['constat'] !== null) {
            $update['description_panne'] = $validated['constat'];
        }
        if (isset($validated['houres'])) {
            $update['houres'] = (string) (int) $validated['houres'];
        }

        $update['status'] = match ($validated['statut']) {
            'en_attente' => InterventionStatus::PLANIFIEE->value,
            'en_cours' => InterventionStatus::EN_COURS->value,
            'termine', 'annule' => InterventionStatus::TERMINEE->value,
        };

        if (!empty($validated['heure_debut'])) {
            $update['start_date'] = Carbon::parse("{$day} {$validated['heure_debut']}");
        }

        if (!empty($validated['heure_fin'])) {
            $update['end_date'] = Carbon::parse("{$day} {$validated['heure_fin']}");
        }

        $intervention->fill($update);
        ClientReportService::fill($intervention, $validated);
        $intervention->save();

        // Intervention terminée et signée (ou client absent) → compte rendu au client, une seule fois
        if ($validated['statut'] === 'termine') {
            ClientReportService::rememberSiteEmail($intervention);
            ClientReportService::sendAfterResponse($intervention);
        }

        return response()->json([
            'success' => true,
            'message' => 'Intervention mise à jour avec succès',
            'data' => [
                'id' => $intervention->id,
                'status' => $intervention->status,
                'is_synced' => $intervention->is_synced,
                'signed' => (bool) ($intervention->signature_path || $intervention->signature_refusal),
            ],
        ]);
    }
}
