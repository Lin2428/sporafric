<?php

namespace App\Filament\Resources\ContractFactureResource\Pages;

use App\Filament\Resources\ContractFactureResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListContractFactures extends ListRecords
{
    protected static string $resource = ContractFactureResource::class;
    protected static ?string $title  = "Factures";

    protected function getHeaderActions(): array
    {
        return [
            //Actions\CreateAction::make()
            //->label('Nouvelle facture'),
        ];
    }
}
