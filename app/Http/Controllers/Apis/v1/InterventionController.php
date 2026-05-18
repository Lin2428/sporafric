<?php

namespace App\Http\Controllers\Apis\v1;

use App\Enum\InterventionStatus;
use App\Enum\InterventionType;
use App\Http\Controllers\Controller;
use App\Models\Intervention;
use App\Models\InterventionFiche;
use http\Env;
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
                'nex_vidange' => $intervention->generator->next_vidange,
                'site' => $intervention->site ?? $intervention->contract?->generators?->first()?->pivot?->site ?? $intervention->devis?->generators?->first()?->pivot?->site,
                'phone' => $intervention->contract?->generators?->first()?->pivot?->contact_phone ?? $intervention->devis?->generators?->first()?->pivot?->contact_phone ?? $intervention->customer?->contact_c_phone,
                'images' => $intervention->fiches->map(function (InterventionFiche $fiche) {
                    $data = [];
                    if ($fiche->fiche != null && !str_contains($fiche->fiche, ".pdf")) {
                        $data = [
                            'id' => $fiche->id,
                            'intervention_id' => $fiche->intervention_id,
                            'url' => Env("APP_URL")."/storage/devis/".$fiche->fiche,
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
}
