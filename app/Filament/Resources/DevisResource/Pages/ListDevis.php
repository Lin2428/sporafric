<?php

namespace App\Filament\Resources\DevisResource\Pages;

use App\Enum\GeneratorStatus;
use App\Filament\Resources\DevisResource;
use App\Http\Controllers\OdooController;
use App\Models\Customer;
use App\Models\Devis;
use App\Models\DevisGenerator;
use App\Models\Generator;
use App\Models\User;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ViewField;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Log;

class ListDevis extends ListRecords
{
    protected static string $resource = DevisResource::class;
    public bool $category;
    protected function getHeaderActions(): array
    {
        return [
            //Actions\CreateAction::make(),
            Actions\Action::make('synchronize')
                ->label('Synchroniser')
                ->icon('heroicon-o-arrow-path')
                ->color('primary')
                ->modalHeading('Synchroniser les devis')
                ->modal()
                ->form([
                    Select::make('syncronize-type')
                        ->label('Que voulez-vous synchroniser ?')
                        ->reactive()
                        ->required()
                        ->options([
                            false => 'Devis non synchronisés',
                            true => 'Tout les devis',
                        ])
                        ->afterStateUpdated(function ($state) {
                               $this->category = $state;
                        }),
                ])
                ->action(function () {
                     set_time_limit(120);
                     try {
                            OdooController::syncronizeDevis($this->category);
                        } catch (\Throwable $th) {
                            Notification::make()
                            ->title('Une erreur est survenue lors de la synchronisation !')
                            ->danger()
                            ->send();

                         Notification::make()
                             ->title("Synchronisation des Devis échouée")
                             ->body("La synchronisation des devis initiée par " .  auth()->user()->name . " a échouée")
                             ->danger()
                             ->icon('heroicon-o-arrow-path')
                             ->sendToDatabase($this->superReceiver());
                         Log::warning('Synchronisation des devis échouée, exécutée par '.  auth()->user()->name . ' à ' . now());
                            return;
                        }

                    Notification::make()->title('Synchronisation terminée')
                        ->body('Les devis ont été synchronisés avec succès.')
                        ->success()->send();

                    Notification::make()
                        ->title("Synchronisation des Devis réussi")
                        ->body("La synchronisation des devis initiée par " .  auth()->user()->name . " a réussi")
                        ->success()
                        ->icon('heroicon-o-arrow-path')
                        ->sendToDatabase($this->superReceiver());

                    Log::info('Synchronization des devis réussi, éxecutée par '.  auth()->user()->name . 'à' . now());
                })
                ->visible(auth()->user()->hasPermissionTo('create_devis')),
        ];
    }

    public static function superReceiver(): mixed
    {
        return  User::role(['super_admin', 'Superviseur', 'Secrétaire'])->get();
    }
}
