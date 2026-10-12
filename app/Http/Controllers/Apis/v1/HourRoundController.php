<?php

namespace App\Http\Controllers\Apis\v1;

use App\Enum\HourRoundStatus;
use App\Http\Controllers\Controller;
use App\Models\HourReading;
use App\Models\HourRound;
use App\Services\HourReadingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class HourRoundController extends Controller
{
    /**
     * Tournées de relevés d'heures assignées au technicien connecté :
     * toutes celles non terminées + les terminées des 7 derniers jours.
     */
    public function index(): JsonResponse
    {
        $user = auth()->user();

        $rounds = HourRound::query()
            ->where('technicien_id', $user->technicien_id ?? 0)
            ->where(function ($query) {
                $query->where('status', '<>', HourRoundStatus::TERMINEE->value)
                    ->orWhereDate('date', '>=', now()->subDays(7)->toDateString());
            })
            ->with([
                'technicien:id,name',
                'readings.customer:id,name',
                'readings.generator' => fn ($q) => $q->without(['contractGenerator', 'etat', 'files']),
            ])
            ->orderBy('date')
            ->get();

        $data = $rounds->map(fn (HourRound $round) => [
            'id'           => $round->id,
            'date'         => $round->date?->toDateString(),
            'status'       => $round->status,
            'status_label' => HourRoundStatus::tryFrom((string) $round->status)?->label(),
            'notes'        => $round->notes,
            'technicien'   => $round->technicien?->name,
            'readings'     => $round->readings->map(fn (HourReading $reading) => $this->formatReading($reading))->values(),
        ])->values();

        return response()->json([
            'success' => true,
            'message' => 'Tournées de relevés chargées avec succès',
            'data'    => $data,
        ]);
    }

    /**
     * Enregistre (ou corrige) la valeur relevée sur une ligne de tournée.
     */
    public function storeReading(Request $request, int $id): JsonResponse
    {
        $user = auth()->user();

        $reading = HourReading::query()
            ->whereHas('round', fn ($q) => $q->where('technicien_id', $user->technicien_id ?? 0))
            ->find($id);

        if (! $reading) {
            return response()->json([
                'success' => false,
                'message' => 'Ligne de relevé introuvable',
                'data'    => null,
            ], 404);
        }

        $validated = $request->validate([
            'houres'  => ['required', 'integer', 'min:0'],
            'read_at' => ['nullable', 'date'],
            'note'    => ['nullable', 'string', 'max:1000'],
        ]);

        $reading = HourReadingService::record(
            $reading,
            (int) $validated['houres'],
            // L'app envoie de l'ISO UTC : ramener au fuseau applicatif avant stockage.
            isset($validated['read_at']) ? Carbon::parse($validated['read_at'])->setTimezone(config('app.timezone')) : null,
            $validated['note'] ?? null,
            $user->id,
        );

        return response()->json([
            'success' => true,
            'message' => 'Relevé enregistré avec succès',
            'data'    => $this->formatReading($reading->fresh(['customer:id,name', 'generator'])),
        ]);
    }

    protected function formatReading(HourReading $reading): array
    {
        $generator = $reading->generator;

        return [
            'id'              => $reading->id,
            'hour_round_id'   => $reading->hour_round_id,
            'generator_id'    => $reading->generator_id,
            'generator'       => $generator?->name,
            'power'           => $generator?->power,
            'adresse'         => $generator?->adresse,
            'customer'        => $reading->customer?->name,
            'site'            => $reading->site,
            'code_site'       => $reading->code_site,
            'position'        => $reading->position,
            // Valeurs connues côté groupe (avant relevé) pour l'affichage terrain.
            'last_houres'     => is_numeric($generator?->houres) ? (int) $generator->houres : null,
            'prochain_visite' => $generator?->prochain_visite,
            'next_vidange'    => $generator?->next_vidange,
            // Valeurs relevées
            'houres'          => $reading->houres,
            'previous_houres' => $reading->previous_houres,
            'read_at'         => $reading->read_at,
            'note'            => $reading->note,
        ];
    }
}
