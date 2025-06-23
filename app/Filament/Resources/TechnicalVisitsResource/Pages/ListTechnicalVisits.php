<?php

namespace App\Filament\Resources\TechnicalVisitsResource\Pages;

use App\Filament\Resources\TechnicalVisitsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use ArielMejiaDev\FilamentPrintable\Actions\PrintAction;

class ListTechnicalVisits extends ListRecords
{
    protected static string $resource = TechnicalVisitsResource::class;
    protected static ?string $title = 'Visites techniques';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            // PrintAction::make(),
        ];
    }
}
