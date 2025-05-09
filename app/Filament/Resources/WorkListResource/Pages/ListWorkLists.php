<?php

namespace App\Filament\Resources\WorkListResource\Pages;

use App\Filament\Resources\WorkListResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWorkLists extends ListRecords
{
    protected static string $resource = WorkListResource::class;

    protected static ?string $title = 'Tâches de Maintenance';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nouvelle tâche')
                ->icon('heroicon-o-plus')
                ->modalActions()
                ->modalWidth('md')
                ->modalHeading('Ajouter une tâche'),
        ];
    }
}
