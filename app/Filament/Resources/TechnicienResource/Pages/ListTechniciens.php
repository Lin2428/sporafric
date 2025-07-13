<?php

namespace App\Filament\Resources\TechnicienResource\Pages;

use App\Filament\Resources\TechnicienResource;
use App\Http\Controllers\OdooController;
use App\Models\Technicien;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

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
                        return;
                    }

                    Notification::make()
                        ->title('Techniciens synchronisés')
                        ->success()
                        ->send();
                })
                ->requiresConfirmation()
                ->color('primary')
                ->visible(auth()->user()->hasPermissionTo('create_technicien')),
        ];
    }
}
