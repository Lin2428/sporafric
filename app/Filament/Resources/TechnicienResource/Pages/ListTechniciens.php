<?php

namespace App\Filament\Resources\TechnicienResource\Pages;

use App\Filament\Resources\TechnicienResource;
use App\Http\Controllers\OdooController;
use App\Models\Technicien;
use App\Models\User;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Log;

class ListTechniciens extends ListRecords
{
    protected static string $resource = TechnicienResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make()
            // ->label('Ajouter un technicien')
            //     ->modalHeading('Ajouter un technicien')
            //     ->modalActions()
            //     ->modalWidth('md'),

            Actions\Action::make('sync')
                ->label('Synchroniser')
                ->icon('heroicon-o-arrow-path')
                ->action(function () {
                    set_time_limit(120);
                    try {
                        OdooController::syncronizeTechnicians();
                    } catch (\Throwable $th) {
                        Notification::make()
                            ->title('Une erreur est survenue lors de la synchronisation !')
                            ->danger()
                            ->send();

                        Notification::make()
                            ->title("Synchronisation des Techniciens échouée")
                            ->body("La synchronisation des techniciens initiée par " .  auth()->user()->name . " a échouée")
                            ->danger()
                            ->icon('heroicon-o-arrow-path')
                            ->sendToDatabase($this->superReceiver());

                        Log::warning('Synchronisation des techniciens échouée, exécutée par '.  auth()->user()->name . ' à ' . now());

                        return;
                    }

                    Notification::make()
                        ->title('Techniciens synchronisés')
                        ->success()
                        ->send();

                    Notification::make()
                        ->title("Synchronisation des Techniciens réussi")
                        ->body("La synchronisation des techniciens initiée par " .  auth()->user()->name . " a réussi")
                        ->success()
                        ->icon('heroicon-o-arrow-path')
                        ->sendToDatabase($this->superReceiver());

                    Log::info('Synchronization des techniciens réussi, éxecutée par '.  auth()->user()->name . 'à' . now());
                })
                ->requiresConfirmation()
                ->color('primary')
                ->visible(auth()->user()->hasPermissionTo('create_technicien')),
        ];
    }

    public static function superReceiver(): mixed
    {
        return  User::role(['super_admin', 'Superviseur', 'Secrétaire'])->get();
    }
}
