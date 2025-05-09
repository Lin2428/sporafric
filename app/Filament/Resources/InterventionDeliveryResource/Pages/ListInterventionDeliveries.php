<?php

namespace App\Filament\Resources\InterventionDeliveryResource\Pages;

use App\Filament\Resources\InterventionDeliveryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInterventionDeliveries extends ListRecords
{
    protected static string $resource = InterventionDeliveryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nouvelle intervention'),
        ];
    }
}
