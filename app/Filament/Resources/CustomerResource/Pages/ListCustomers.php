<?php
namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use App\Http\Controllers\OdooController;
use App\Models\Customer;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

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
                    $data = OdooController::syncronizeClient();
               
                    foreach ($data as $client) {
                    
                        Customer::updateOrCreate(
                            [
                                'odoo_id' => $client['id'],
                            ],
                            [
                                'odoo_id'         => $client['id'],
                                'name'            => $client['name'],
                                'contact_c_name'  => $client['name'],
                                'contact_c_phone' => $client['phone'],
                                'contact_cemail'  => $client['email'],
                                'city'            => $client['city'],
                            ]
                        );
                    }

                    Notification::make()
                        ->title('Clients synchronisés')
                        ->success()
                        ->send();
                })
                ->requiresConfirmation()
                ->color('primary'),
        ];
    }
}
