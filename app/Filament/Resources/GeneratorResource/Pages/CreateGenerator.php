<?php

namespace App\Filament\Resources\GeneratorResource\Pages;

use App\Filament\Resources\GeneratorResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateGenerator extends CreateRecord
{
    protected static string $resource = GeneratorResource::class;
    protected static ?string $title = 'Ajouter un groupe électrogène';

      protected function mutateFormDataBeforeCreate(array $data): array
    {
            $houres = $data['houres'];
            $data['next_vidange'] = $data['prochain_visite'] -  $houres;
            $data['vidange'] =  $data['next_vidange'] > 30;

            return $data;
    }
}
