<?php

namespace App\Filament\Resources\TechnicienResource\Pages;

use App\Filament\Resources\TechnicienResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTechniciens extends ListRecords
{
    protected static string $resource = TechnicienResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Ajouter un technicien')
                ->modalHeading('Ajouter un technicien')
                ->modalActions()
                ->modalWidth('md'),
        ];
    }
}
