<?php

namespace App\Filament\Resources\InterventionResource\Pages;

use App\Filament\Resources\InterventionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;

class ListInterventions extends ListRecords
{
    protected static string $resource = InterventionResource::class;

    protected static ?string $title = 'Interventions';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nouvelle intervention'),
        ];
    }
    public function getTabs(): array
    {
        return [
            Tab::make('Tout'),
            Tab::make('Sous contrat')->query(
                fn($query) =>
                $query->where('type_activite', '=', 1)
            ),
            Tab::make('Hors contrat')->query(
                fn($query) =>
                $query->where('type_activite', 0)
            ),

        ];
    }
}
