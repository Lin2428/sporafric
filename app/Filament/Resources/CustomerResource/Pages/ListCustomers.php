<?php
namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use App\Http\Controllers\OdooController;
use App\Models\Customer;
use App\Models\User;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Log;

class ListCustomers extends ListRecords
{
    protected static string $resource = CustomerResource::class;

    protected static ?string $title = 'Clients';

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make()
            //     ->label('Ajouter un client')
            //     ->modalActions(),
            Actions\Action::make('sync')
                ->label('Synchroniser')
                ->icon('heroicon-o-arrow-path')
                ->action(function () {
                        set_time_limit(300);
                    try {
                        OdooController::syncronizeClient();
                    } catch (\Throwable $th) {
                         Notification::make()
                            ->title('Une erreur est survenue lors de la synchronisation !')
                            ->danger()
                            ->send();

                        Notification::make()
                            ->title("Synchronisation des Clients échouée")
                            ->body("La synchronisation des cliens initiée par " .  auth()->user()->name . " a échouée")
                            ->danger()
                            ->icon('heroicon-o-arrow-path')
                            ->sendToDatabase($this->superReceiver());

                        Log::warning('Synchronisation des clients échouée, exécutée par '.  auth()->user()->name );
                        return;
                    }

                    Notification::make()
                        ->title('Clients synchronisés')
                        ->success()
                        ->send();

                    Notification::make()
                        ->title("Synchronisation des clients réussi")
                        ->body("La synchronisation des clients initiée par " .  auth()->user()->name . " a réussi")
                        ->success()
                        ->icon('heroicon-o-arrow-path')
                        ->sendToDatabase($this->superReceiver());

                    Log::info('Synchronization des clients réussi, éxecutée par '.  auth()->user()->name . 'à' . now());
                })
                ->requiresConfirmation()
                ->color('primary')
                ->visible(auth()->user()->hasPermissionTo('create_customer')),
        ];
    }

    public static function superReceiver(): mixed
    {
        return  User::role(['super_admin', 'Superviseur', 'Secrétaire'])->get();
    }
}
