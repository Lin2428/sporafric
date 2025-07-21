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

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $houres = $data['houres'];
        $data['next_vidange'] = $data['prochain_visite'] -  $houres;
        $data['vidange'] =  $data['next_vidange'] > 30;

        return $data;
    }
}
