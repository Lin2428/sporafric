<?php

namespace App\Filament\Resources\GeneratorResource\Pages;

use App\Enum\GeneratorStatus;
use App\Filament\Resources\GeneratorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;

class ListGenerators extends ListRecords
{
    protected static string $resource = GeneratorResource::class;

    protected static ?string $title = 'Groupes électrogènes';

    public function getTabs(): array
    {
        return  [
            Tab::make("Tout"),

            Tab::make("Actifs")->query(
                fn($query) =>
                $query->where('status', '<>', GeneratorStatus::INDISPONIBLE->value)
            ),

            Tab::make("Inactifs")->query(
                fn($query) =>
                $query->where('status', '=', GeneratorStatus::INDISPONIBLE->value)
            ),

            
            
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Ajouter')
                ->icon('heroicon-o-plus'),
        ];
    }
}
