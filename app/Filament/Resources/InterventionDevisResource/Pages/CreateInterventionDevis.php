<?php

namespace App\Filament\Resources\InterventionDevisResource\Pages;

use App\Enum\GeneratorStatus;
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

         if($data['type'] == InterventionType::REMPLACEMENT->value){
                Generator::where('id', $data['generator_id'])
            ->update([
                'status' => GeneratorStatus::EN_REVU->value
            ]);

            Generator::where('id', $data['new_generator_id'])
            ->update([
                'status' => GeneratorStatus::EN_LOCATION->value
            ]);

            DevisGenerator::where('devis_id', $data['devis_id'])
            ->where('generator_id', $data['generator_id'])
            ->update([
                'generator_id' => $data['new_generator_id'],
                'old_generator_id' => $data['generator_id'],
            ]);
        }

        return $data;

    }

        protected function afterCreate()
{
    $data = $this->form->getState();
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
