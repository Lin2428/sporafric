<?php

namespace App\Filament\Resources\Location\QuartierResource\Pages;

use App\Filament\Resources\Location\QuartierResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListQuartiers extends ListRecords
{
    protected static string $resource = QuartierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
