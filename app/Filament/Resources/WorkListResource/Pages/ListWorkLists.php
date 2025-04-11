<?php

namespace App\Filament\Resources\WorkListResource\Pages;

use App\Filament\Resources\WorkListResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWorkLists extends ListRecords
{
    protected static string $resource = WorkListResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
