<?php

namespace App\Filament\Resources\InterventionDevisResource\Pages;

use App\Filament\Resources\InterventionDevisResource;
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
        }

        return $data;

    }
}
