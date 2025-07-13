<?php

namespace App\Filament\Resources\GeneratorResource\Pages;

use App\Enum\GeneratorStatus;
use App\Filament\Resources\GeneratorResource;
use App\Http\Controllers\OdooController;
use App\Models\Generator;
use Filament\Actions;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ViewField;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;

class ListGenerators extends ListRecords
{
    protected static string $resource = GeneratorResource::class;

    protected static ?string $title = 'Groupes électrogènes';
    public bool $category;

    
    public function getTabs(): array
    {
        return  [
            Tab::make("Tout"),

            Tab::make("En location")->query(
                fn($query) =>
                $query->where('status', '=', GeneratorStatus::EN_LOCATION->value)
            ),

            Tab::make("Inactifs")->query(
                fn($query) =>
                $query->where('status', '<>', GeneratorStatus::EN_LOCATION->value)
            ),



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

                    try {
                         OdooController::syncronizeGenerator($this->category);
                    } catch (\Throwable $th) {
                        Notification::make()
                            ->title('Une erreur est survenue lors de la synchronisation !')
                            ->danger()
                            ->send();
                        return;
                    }

                    Notification::make()
                        ->success()
                        ->title('Synchronisation effectuée')
                        ->send();
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
}
