<?php

namespace App\Filament\Resources\PieceResource\Pages;

use App\Filament\Resources\PieceResource;
use App\Http\Controllers\OdooController;
use App\Models\Piece;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListPieces extends ListRecords
{
    protected static string $resource = PieceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //Actions\CreateAction::make(),
            Actions\Action::make('synchronuis')
                ->label('Synchroniser')
                ->icon('heroicon-o-arrow-path')
                ->action(function() {
                          set_time_limit(120);
                    try {
                        OdooController::syncronizePieces();
                    } catch (\Throwable $th) {
                        Notification::make()
                            ->title('Une erreur est survenue lors de la synchronisation !')
                            ->danger()
                            ->send();
                        return;
                    }

                    Notification::make()
                        ->title('Pieces synchronisés')
                        ->success()
                        ->send();

                })->requiresConfirmation(),
        ];
    }
}
