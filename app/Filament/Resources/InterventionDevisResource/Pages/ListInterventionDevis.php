<?php

namespace App\Filament\Resources\InterventionDevisResource\Pages;

use App\Filament\Resources\InterventionDevisResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInterventionDevis extends ListRecords
{
    protected static string $resource = InterventionDevisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Nouvelle Intervention'),
        ];
    }
}
