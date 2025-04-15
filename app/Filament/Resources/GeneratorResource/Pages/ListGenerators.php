<?php

namespace App\Filament\Resources\GeneratorResource\Pages;

use App\Filament\Resources\GeneratorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGenerators extends ListRecords
{
    protected static string $resource = GeneratorResource::class;

    protected static ?string $title = 'Liste des groupes électrogènes';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Ajouter')
                ->icon('heroicon-o-plus'),
        ];
    }
}
