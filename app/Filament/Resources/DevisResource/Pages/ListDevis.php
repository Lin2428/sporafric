<?php

namespace App\Filament\Resources\DevisResource\Pages;

use App\Enum\GeneratorStatus;
use App\Filament\Resources\DevisResource;
use App\Http\Controllers\OdooController;
use App\Models\Customer;
use App\Models\Devis;
use App\Models\DevisGenerator;
use App\Models\Generator;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ViewField;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

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

                            return;
                        }             

                    Notification::make()->title('Synchronisation terminée')->body('Les devis ont été synchronisés avec succès.')->success()->send();
                })
                ->visible(auth()->user()->hasPermissionTo('create_devis')),
        ];
    }
}
