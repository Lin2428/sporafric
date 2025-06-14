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
                    $data = OdooController::syncronizePieces();

                    foreach($data as $piece)
                    {
                        Piece::updateOrCreate(
                            [
                                'odoo_id' => $piece['id']
                            ],
                            [
                                'odoo_id' => $piece['id'],
                                'reference' => $piece['name'],
                                'designation' => $piece['default_code'],
                                'duree_vie' => 0,
                                'pr' => $piece['standard_price'],
                                'pv' => $piece['list_price'],
                            ]);
                    }

                    Notification::make()
                        ->title('Pieces synchronisés')
                        ->success()
                        ->send();

                })->requiresConfirmation(),
        ];
    }
}
