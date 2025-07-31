<?php

namespace App\Filament\Resources\RevisionResource\Pages;

use App\Filament\Resources\RevisionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRevision extends EditRecord
{
    protected static string $resource = RevisionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
