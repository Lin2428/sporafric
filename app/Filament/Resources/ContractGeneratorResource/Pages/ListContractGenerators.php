<?php

namespace App\Filament\Resources\ContractGeneratorResource\Pages;

use App\Filament\Resources\ContractGeneratorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListContractGenerators extends ListRecords
{
    protected static string $resource = ContractGeneratorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
