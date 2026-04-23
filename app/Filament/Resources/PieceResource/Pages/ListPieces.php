<?php

namespace App\Filament\Resources\PieceResource\Pages;

use App\Filament\Resources\PieceResource;
use App\Http\Controllers\OdooController;
use App\Models\Piece;
use App\Models\User;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Log;

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

                        Notification::make()
                            ->title("Synchronisation des Pièces échouée")
                            ->body("La synchronisation des pièces initiée par " .  auth()->user()->name . " a échouée")
                            ->danger()
                            ->icon('heroicon-o-arrow-path')
                            ->sendToDatabase($this->superReceiver());

                        Log::warning('Synchronisation des pièces échouée, exécutée par '.  auth()->user()->name . ' à ' . now());
                        return;
                    }

                    Notification::make()
                        ->title('Pieces synchronisés')
                        ->success()
                        ->send();

                    Notification::make()
                        ->title("Synchronisation des Pièces réussi")
                        ->body("La synchronisation des pièces initiée par " .  auth()->user()->name . " a réussi")
                        ->success()
                        ->icon('heroicon-o-arrow-path')
                        ->sendToDatabase($this->superReceiver());

                    Log::info('Synchronization des pièces réussi, éxecutée par '.  auth()->user()->name . 'à' . now());

                })->requiresConfirmation(),
        ];
    }

    public static function superReceiver(): mixed
    {
        return  User::role(['super_admin', 'Superviseur', 'Secrétaire'])->get();
    }
}
