<?php

namespace App\Filament\Resources\GeneratorResource\Pages;

use App\Filament\Resources\GeneratorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGenerator extends EditRecord
{
    protected static string $resource = GeneratorResource::class;

    protected static ?string $title = 'Modifier le groupe électrogène';

    protected function getHeaderActions(): array
    {

        return [
            // Actions\DeleteAction::make(),
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
