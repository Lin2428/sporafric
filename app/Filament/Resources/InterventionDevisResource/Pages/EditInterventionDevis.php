<?php

namespace App\Filament\Resources\InterventionDevisResource\Pages;

use App\Filament\Resources\InterventionDevisResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInterventionDevis extends EditRecord
{
    protected static string $resource = InterventionDevisResource::class;
    protected static ?string $title = 'Modifier une intervention';
    
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
