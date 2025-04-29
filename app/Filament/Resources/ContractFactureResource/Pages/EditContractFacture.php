<?php

namespace App\Filament\Resources\ContractFactureResource\Pages;

use App\Filament\Resources\ContractFactureResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditContractFacture extends EditRecord
{
    protected static string $resource = ContractFactureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
