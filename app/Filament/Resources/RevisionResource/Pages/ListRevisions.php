<?php

namespace App\Filament\Resources\RevisionResource\Pages;

use App\Filament\Resources\RevisionResource;
use App\Http\Controllers\OdooController;
use App\Models\Generator;
use App\Models\Intervention;
use App\Utils\NumberUtils;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use ArielMejiaDev\FilamentPrintable\Actions\PrintAction;
use Filament\Notifications\Notification;

class ListRevisions extends ListRecords
{
    protected static string $resource = RevisionResource::class;
    protected static ?string $title = " ";

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->modalActions()
            ->modalWidth('6xl')
            ->modalHeading('Nouvelle intervention')
            ->color('info')
            ->label('Nouvelle intervention')
            ->action(function ($data) {
                    $data['type_service'] = 2; // Assuming 1 is for Maintenance
                    $data['numero'] = NumberUtils::intevention_numero('INT-CINT');

                $houres = $data['houres'];
                $nexTvidange = $data['prochain_visite'] -  $houres;
                $vidange =  $nexTvidange > 30;

                $intervention = Intervention::create($data);

                Generator::where('id', $data['generator_id'])
                    ->update([
                        'houres' => $data['houres'],
                        'next_vidange' => $nexTvidange,
                        'prochain_visite' => $data['prochain_visite'],
                        'vidange' => $vidange
                    ]);

                foreach ($data['pieces'] as $pieceData) {
                        $intervention->pieces()->attach(
                $pieceData['piece_id'],
                [
                    'qty' => $pieceData['qty'],
                    'price' => $pieceData['price'] ?? 0,
                    'generator_id' => $data['generator_id'] ?? null,
                ]
            );
        }
            }),

            Action::make('synchro')
            ->label('Synchroniser les consos internes')
            ->icon('heroicon-o-arrow-path')
             ->action(function () {
                     set_time_limit(120);
                     try {
                            OdooController::syncronizeConsoInterne();  
                        } catch (\Throwable $th) {
                            Notification::make()
                            ->title('Une erreur est survenue lors de la synchronisation !')
                            ->danger()
                            ->send();

                            return;
                        }             

                    Notification::make()->title('Synchronisation terminée')->body('Les devis ont été synchronisés avec succès.')->success()->send();
                }),
        ];
    }
}
