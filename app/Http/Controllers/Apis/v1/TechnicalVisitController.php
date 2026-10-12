<?php

namespace App\Http\Controllers\Apis\v1;

use App\Enum\InterventionTypeService;
use App\Http\Controllers\Controller;
use App\Models\Generator;
use App\Models\TechnicalVisits;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TechnicalVisitController extends Controller
{
    /**
     * Enregistre une visite technique (ex-« ronde ») réalisée sur le terrain
     * par un technicien depuis l'app mobile.
     */
    public function store(Request $request): JsonResponse
    {
        $user = auth()->user();

        $boolControls = collect(range(1, 16))
            ->mapWithKeys(fn ($n) => ["control_{$n}" => ['nullable', 'boolean']])
            ->toArray();

        $validated = $request->validate([
            'generator_id' => ['required', 'integer', 'exists:generators,id'],
            'date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
            'control_battery' => ['nullable', 'integer', 'in:1,2,3'],
            'control_circuit' => ['nullable', 'numeric'],
            'control_frequence' => ['nullable', 'numeric'],
            'control_tension' => ['nullable', 'array'],
            'control_tension_2' => ['nullable', 'array'],
            'control_intensite' => ['nullable', 'array'],
            ...$boolControls,
        ]);

        $generator = Generator::query()->find($validated['generator_id']);

        // type_service / contrat / devis déduits du groupe électrogène.
        $contractGenerator = $generator?->contractGenerator;
        $devisGenerator = $generator?->devisGenerator;

        if ($contractGenerator) {
            $typeService = InterventionTypeService::MAINTENANCE->value;
            $contractId = $contractGenerator->contract_id;
            $devisId = null;
        } elseif ($devisGenerator) {
            $typeService = InterventionTypeService::LOCATION->value;
            $contractId = null;
            $devisId = $devisGenerator->devis_id;
        } else {
            $typeService = InterventionTypeService::MAINTENANCE->value;
            $contractId = null;
            $devisId = null;
        }

        $visit = TechnicalVisits::create([
            'type_service' => $typeService,
            'contract_id' => $contractId,
            'devis_id' => $devisId,
            'generator_id' => $validated['generator_id'],
            'date' => $validated['date'],
            'notes' => $validated['notes'] ?? null,
            'user_id' => $user->id,
            'control_1' => $validated['control_1'] ?? null,
            'control_2' => $validated['control_2'] ?? null,
            'control_3' => $validated['control_3'] ?? null,
            'control_4' => $validated['control_4'] ?? null,
            'control_5' => $validated['control_5'] ?? null,
            'control_6' => $validated['control_6'] ?? null,
            'control_7' => $validated['control_7'] ?? null,
            'control_8' => $validated['control_8'] ?? null,
            'control_9' => $validated['control_9'] ?? null,
            'control_10' => $validated['control_10'] ?? null,
            'control_11' => $validated['control_11'] ?? null,
            'control_12' => $validated['control_12'] ?? null,
            'control_13' => $validated['control_13'] ?? null,
            'control_14' => $validated['control_14'] ?? null,
            'control_15' => $validated['control_15'] ?? null,
            'control_16' => $validated['control_16'] ?? null,
            'control_battery' => $validated['control_battery'] ?? null,
            'control_circuit' => $validated['control_circuit'] ?? null,
            'control_frequence' => $validated['control_frequence'] ?? null,
            'control_tension' => $validated['control_tension'] ?? null,
            'control_tension_2' => $validated['control_tension_2'] ?? null,
            'control_intensite' => $validated['control_intensite'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Visite technique enregistrée avec succès',
            'data' => [
                'id' => $visit->id,
                'generator_id' => $visit->generator_id,
                'date' => $visit->date,
                'created_at' => $visit->created_at,
            ],
        ]);
    }
}
