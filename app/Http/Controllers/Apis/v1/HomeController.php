<?php

namespace App\Http\Controllers\Apis\v1;

use App\Enum\InterventionStatus;
use App\Enum\InterventionType;
use App\Http\Controllers\Controller;
use App\Models\Intervention;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = auth()->user();

        $interventionsQuery = Intervention::query()
            ->whereHas("interventionTechniciens", function ($query) use ($user) {
                return $query->where("technicien_id", $user->technicien_id);
            })->whereDate("date_planifiee", "=", now());

        $countsByType = (clone $interventionsQuery)
            ->select('type', DB::raw('count(*) as total'))
            ->whereNotNull('type')
            ->groupBy('type')
            ->pluck('total', 'type');

        $interventionsByType = collect(InterventionType::cases())
            ->map(fn (InterventionType $type) => [
                'type' => $type->value,
                'label' => $type->label(),
                'total' => (int) ($countsByType[$type->value] ?? 0),
            ])
            ->values();

        $nextInterventions = (clone $interventionsQuery)
            ->with(['customer:id,name', 'generator:id,name,reference'])
            ->whereNotNull('date_planifiee')
            ->whereDate('date_planifiee', now())
            ->where('cancelled', false)
            ->where('status', '!=', InterventionStatus::TERMINEE->value)
            ->orderBy('date_planifiee')
            ->limit(5)
            ->get()
            ->map(fn (Intervention $intervention) => [
                'id' => $intervention->id,
                'date_planifiee' => $intervention->date_planifiee,
                'type' => $intervention->type,
                'type_label' => InterventionType::tryFrom((string) $intervention->type)?->label(),
                'status' => $intervention->status,
                'status_label' => InterventionStatus::tryFrom((string) $intervention->status)?->label(),
                'customer' => $intervention->devis?->customer?->name ?? $intervention->contract?->customer?->name ?? $intervention->customer?->name,
                'generator' => $intervention->generator?->name,
                'phone' => $intervention->contract?->generators?->first()?->pivot?->contact_phone ?? $intervention->devis?->generators?->first()?->pivot?->contact_phone ?? $intervention->customer?->contact_c_phone,
                'site' => $intervention->site ?? $intervention->contract?->generators?->first()?->pivot?->site ?? $intervention->devis?->generators?->first()?->pivot?->site,
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Statistiques de la home.',
            'data' => [
                'total_interventions' => (clone $interventionsQuery)->count(),
                'interventions_by_type' => $interventionsByType,
                'next_interventions' => $nextInterventions,
            ],
        ]);
    }
}
