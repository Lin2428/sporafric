<?php

namespace App\Filament\Resources\ContractGeneratorResource\Pages;

use App\Filament\Resources\ContractGeneratorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;

class ListContractGenerators extends ListRecords
{
    protected static string $resource = ContractGeneratorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    public function getTabs(): array
    {
        return [
            Tab::make('Tout'),
            Tab::make('Sous contrat')->query(
                fn($query) =>
                $query->where(function ($q) {
                    $q->whereHas('contractGenerator')
                        ->orWhereHas('oldContractGenerator');
                })
            ),
            Tab::make('Hors contrat')->query(
                fn($query) =>
                $query->where(function ($q) {
                    $q->whereDoesntHave('contractGenerator')
                        ->whereDoesntHave('oldContractGenerator');
                })
            ),

        ];
    }
}
