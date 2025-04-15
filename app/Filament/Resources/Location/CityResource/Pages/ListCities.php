<?php

namespace App\Filament\Resources\Location\CityResource\Pages;

use App\Filament\Resources\Location\CityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCities extends ListRecords
{
    protected static string $resource = CityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Ajouter une ville')
                ->modalActions()
                ->modalHeading('Ajouter une ville')
                ->modalWidth('md'),
        ];
    }
}
