<?php

namespace App\Filament\Resources\HourRoundResource\Pages;

use App\Filament\Resources\HourRoundResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHourRound extends EditRecord
{
    protected static string $resource = HourRoundResource::class;

    protected static ?string $title = 'Tournée de relevés';

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('print')
                ->label('Imprimer la fiche')
                ->icon('heroicon-o-printer')
                ->color('info')
                ->url(fn () => HourRoundResource::getUrl('print', ['record' => $this->getRecord()])),
            Actions\DeleteAction::make(),
        ];
    }
}
