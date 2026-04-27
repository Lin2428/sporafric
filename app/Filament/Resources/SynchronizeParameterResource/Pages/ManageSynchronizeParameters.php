<?php

namespace App\Filament\Resources\SynchronizeParameterResource\Pages;

use App\Filament\Resources\SynchronizeParameterResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageSynchronizeParameters extends ManageRecords
{
    protected static string $resource = SynchronizeParameterResource::class;
    protected static ?string $title = "Paramètres de Synchronisation";

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->modalWidth('4xl'),
        ];
    }
}
