<?php

namespace App\Filament\Resources\OperatorFilterResource\Pages;

use App\Filament\Resources\OperatorFilterResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageOperatorFilters extends ManageRecords
{
    protected static string $resource = OperatorFilterResource::class;
    protected static ?string $title = "Opérateurs de Filtres";
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
