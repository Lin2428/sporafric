<?php

namespace App\Filament\Resources\InterventionResource\Pages;

use App\Filament\Resources\InterventionResource;
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

        return $data;

    }
}
