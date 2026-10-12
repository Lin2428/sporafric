<?php

namespace App\Http\Controllers\Apis\v1;

use App\Enum\GeneratorStatus;
use App\Http\Controllers\Controller;
use App\Models\Generator;
use Illuminate\Http\JsonResponse;

class GeneratorController extends Controller
{
    /**
     * Liste des groupes électrogènes pour le sélecteur des rondes (app mobile).
     */
    public function index(): JsonResponse
    {
        $data = Generator::query()
            ->without(['etat', 'files'])
            ->with([
                'contractGenerator:id,generator_id,contract_id,site,code_site',
                'contractGenerator.contract:id,customer_id',
                'contractGenerator.contract.customer:id,name',
                'devisGenerator.devis.customer:id,name',
            ])
            ->orderBy('name')
            ->get()
            ->map(fn (Generator $generator) => [
                'id' => $generator->id,
                'nom' => $generator->name,
                'reference' => $generator->reference,
                'localisation' => $generator->contractGenerator?->site ?: ($generator->devisGenerator?->site ?: $generator->adresse),
                'contratId' => $generator->contractGenerator?->contract_id,
                'power' => $generator->power,
                'status_label' => GeneratorStatus::tryFrom((string) $generator->status)?->label(),
                // Critères de recherche terrain (le nom du GE seul n'est pas discriminant).
                'client' => $generator->contractGenerator?->contract?->customer?->name
                    ?? $generator->devisGenerator?->devis?->customer?->name,
                'code_site' => $generator->contractGenerator?->code_site ?? $generator->devisGenerator?->code_site,
                'adresse' => $generator->adresse,
            ])
            ->values();

        return response()->json([
            'success' => true,
            'message' => 'Groupes électrogènes chargés avec succès',
            'data' => $data,
        ]);
    }
}
