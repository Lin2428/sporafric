<?php

namespace App\Filament\Resources\GeneratorResource\Pages;

use App\Filament\Resources\GeneratorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGenerator extends EditRecord
{
    protected static string $resource = GeneratorResource::class;

    protected static ?string $title = 'Modifier un groupe électrogène';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
