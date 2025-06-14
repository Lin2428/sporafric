<?php

namespace App\Filament\Resources\ContractGeneratorResource\Pages;

use App\Filament\Resources\ContractGeneratorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditContractGenerator extends EditRecord
{
    protected static string $resource = ContractGeneratorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
