<?php

namespace App\Filament\Resources\InterventionResource\Pages;

use App\Enum\InterventionType;
use App\Filament\Resources\InterventionResource;
use App\Models\Generator;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateIntervention extends CreateRecord
{
    protected static string $resource = InterventionResource::class;

    protected static ?string $title = 'Ajouter une intervention';

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set the default type_service to 'Maintenance' if not provided
        if (!isset($data['type_service'])) {
            $data['type_service'] = 1; // Assuming 1 is for Maintenance
        }

   
         if($data['type_activite'] == '1' &&($data['type'] == InterventionType::RONDE->value || $data['type'] == InterventionType::VIDANGE->value)){
           $houres = $data['houres'];
             $nexTvidange = $data['prochain_visite'] -  $houres;
            $vidange =  $nexTvidange > 30;

            Generator::where('id', $data['generator_id'])
            ->update([
                'houres' => $data['houres'],
                'next_vidange' => $data['next_vidange'],
                'prochain_visite' => $data['prochain_visite'],
                'vidange' => $vidange
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
