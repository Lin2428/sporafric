<?php

namespace App\Filament\Resources\InterventionDevisResource\Pages;

use App\Enum\GeneratorStatus;
use App\Enum\InterventionStatus;
use App\Enum\InterventionType;
use App\Filament\Resources\InterventionDevisResource;
use App\Models\DevisGenerator;
use App\Models\Generator;
use App\Utils\NumberUtils;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateInterventionDevis extends CreateRecord
{
    protected static string $resource = InterventionDevisResource::class;

    protected static ?string $title = 'Ajouter une intervention';

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set the default type_service to 'Maintenance' if not provided
        if (!isset($data['type_service'])) {
            $data['type_service'] = 0; // Assuming 1 is for Maintenance
            $data['numero'] = NumberUtils::intevention_numero('INT-LOC');
        }

        $houres = $data['houres'];
        $nexTvidange = $data['prochain_visite'] -  $houres;
        $vidange =  $nexTvidange > 30;

        Generator::where('id', $data['generator_id'])
            ->update([
                'houres' => $data['houres'],
                'next_vidange' => $nexTvidange,
                'prochain_visite' => $data['prochain_visite'],
                'vidange' => $vidange
            ]);

        if ($data['type'] == InterventionType::REMPLACEMENT->value) {
            Generator::where('id', $data['generator_id'])
                ->update([
                    'status' => GeneratorStatus::EN_REVU->value
                ]);

            Generator::where('id', $data['new_generator_id'])
                ->update([
                    'status' => GeneratorStatus::EN_PRET->value
                ]);

            $oldeGeneratorId =
                DevisGenerator::where('devis_id', '=', $data['devis_id'])
                ->where('generator_id', '=', $data['generator_id'])
                ->where('old_generator_id', '=', $data['new_generator_id'])
                ->first()?->old_generator_id;


            if ($data['new_generator_id'] == $oldeGeneratorId) {
                DevisGenerator::where('devis_id', $data['devis_id'])
                    ->where('generator_id', $data['generator_id'])
                    ->update([
                        'generator_id' => $data['new_generator_id'],
                        'old_generator_id' => null,
                    ]);
                Generator::where('id', $data['new_generator_id'])
                    ->update([
                        'status' => GeneratorStatus::EN_LOCATION->value
                    ]);
            } else {
                DevisGenerator::where('devis_id', $data['devis_id'])
                    ->where('generator_id', $data['generator_id'])
                    ->update([
                        'generator_id' => $data['new_generator_id'],
                        'old_generator_id' => $data['generator_id'],
                    ]);
            }
        }

        return $data;
    }

    protected function afterCreate()
    {
        $data = $this->form->getState();
        if ($data['type'] == InterventionType::RETRAIT->value && $data['status'] == InterventionStatus::TERMINEE->value) {
            $this->record->generator->status = GeneratorStatus::EN_REVU->value;
            $this->record->generator->save();

            DevisGenerator::where('devis_id', $this->record->devis_id)
                ->where('generator_id', $data['generator_id'])
                ->update(['is_retired' => true]);
        }

        foreach ($data['pieces'] as $pieceData) {
            $this->record->pieces()->attach(
                $pieceData['piece_id'],
                [
                    'qty' => $pieceData['qty'],
                    'price' => $pieceData['price'] ?? 0,
                    'generator_id' => $data['generator_id'] ?? null,
                ]
            );
        }
    }
}
