<?php

namespace App\Filament\Resources\HourRoundResource\Pages;

use App\Filament\Resources\HourRoundResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHourRounds extends ListRecords
{
    protected static string $resource = HourRoundResource::class;

    protected static ?string $title = 'Relevés d\'heures';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Nouvelle tournée'),
        ];
    }
}
