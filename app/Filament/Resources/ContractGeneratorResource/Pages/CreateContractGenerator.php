<?php

namespace App\Filament\Resources\ContractGeneratorResource\Pages;

use App\Filament\Resources\ContractGeneratorResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateContractGenerator extends CreateRecord
{
    protected static string $resource = ContractGeneratorResource::class;

    protected static ?string $title = "Créer un nouveau GE de maintenance";

    protected function mutateFormDataBeforeCreate(array $data): array
    {
            $houres = $data['houres'];
            $data['next_vidange'] = $data['prochain_visite'] -  $houres;
            $data['vidange'] =  $data['next_vidange'] > 30;

            return $data;
    }
}
