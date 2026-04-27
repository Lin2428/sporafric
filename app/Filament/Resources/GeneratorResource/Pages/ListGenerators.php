<?php

namespace App\Filament\Resources\GeneratorResource\Pages;

use App\Enum\GeneratorStatus;
use App\Filament\Resources\GeneratorResource;
use App\Http\Controllers\OdooController;
use App\Models\Generator;
use App\Models\User;
use Filament\Actions;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ViewField;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Support\Facades\Log;

class ListGenerators extends ListRecords
{
    protected static string $resource = GeneratorResource::class;

    protected static ?string $title = 'Groupes électrogènes';
    public bool $category;


    public function getTabs(): array
    {
        return  [
            Tab::make("Tout"),

            Tab::make("Disponible")->query(
                fn($query) =>
                $query->where('status', '=', GeneratorStatus::DISPONIBLE->value)
            ),

            Tab::make("En location")->query(
                fn($query) =>
                $query->where('status', '=', GeneratorStatus::EN_LOCATION->value)
            ),

            Tab::make("En révision")->query(
                fn($query) =>
                $query->where('status', '=', GeneratorStatus::EN_REVU->value)
            ),
            Tab::make("En prêt")->query(
                fn($query) =>
                $query->where('status', '=', GeneratorStatus::EN_PRET->value)
            ),

            Tab::make("Indisponible")->query(
                fn($query) =>
                $query->where('status', '=', GeneratorStatus::INDISPONIBLE->value)
            ),

            // Tab::make("Archivé")->query(
            //     fn($query) =>
            //     $query->onlyTrashed(),
            // ),

        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            /*Actions\CreateAction::make()
                ->label('Ajouter')
                ->icon('heroicon-o-plus'),*/
            Actions\Action::make('synchronuis')
                ->label('Synchroniser')
                ->icon('heroicon-o-arrow-path')
                ->modalHeading("Synchroniser les produits")
                ->modal()
                ->form([
                    Select::make('syncronize-type')
                        ->label('Que voulez-vous synchroniser ?')
                        ->reactive()
                        ->options([
                            false => 'Produits non synchronisés',
                            true => 'Tout les produits',
                        ])
                        ->afterStateUpdated(function ($state) {
                            $this->category = $state;
                        }),
                ])
                ->action(function ($data) {
                    set_time_limit(120);
                    $user = auth()->user();
                    try {
                        OdooController::syncronizeGenerator($this->category);
                    } catch (\Throwable $th) {
                        Notification::make()
                            ->title('Une erreur est survenue lors de la synchronisation !')
                            ->danger()
                            ->icon('heroicon-o-arrow-path')
                            ->send();

                        Notification::make()
                            ->title("Synchronisation des GEs échouée")
                            ->body("La synchronisation des GEs initiée par " .  auth()->user()->name . " a échouée")
                            ->danger()
                            ->icon('heroicon-o-arrow-path')
                            ->sendToDatabase($this->superReceiver());

                        Log::warning('Synchronisation des GEs échouée, exécutée par '.  auth()->user()->name );
                        return;
                    }

                    Notification::make()
                        ->success()
                        ->title('Synchronisation effectuée')
                        ->send();

                    Notification::make()
                        ->title("Synchronisation des GEs réussi")
                        ->body("La synchronisation des GEs initiée par " .  auth()->user()->name . " a réussi")
                        ->success()
                        ->icon('heroicon-o-arrow-path')
                        ->sendToDatabase($this->superReceiver());

                    Log::info('Synchronization des GEs réussi, éxecutée par '.  auth()->user()->name . 'à' . now());
                })
                ->modalSubmitActionLabel('Synchroniser')
                ->visible(auth()->user()->hasPermissionTo('create_generator')),
        ];
    }

    public function getProductViewField(): ViewField
    {
        return ViewField::make('product_table')
            ->view('filament.generators.product-table');
    }

    public static function superReceiver(): mixed
    {
        return  User::role(['super_admin', 'Superviseur', 'Secrétaire'])->get();
    }
}
