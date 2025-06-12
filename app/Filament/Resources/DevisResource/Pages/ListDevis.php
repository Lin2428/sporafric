<?php

namespace App\Filament\Resources\DevisResource\Pages;

use App\Filament\Resources\DevisResource;
use App\Http\Controllers\OdooController;
use App\Models\Customer;
use App\Models\Devis;
use App\Models\DevisGenerator;
use App\Models\Generator;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDevis extends ListRecords
{
    protected static string $resource = DevisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //Actions\CreateAction::make(),
            Actions\Action::make('synchronize')
                ->label('Synchroniser')
                ->icon('heroicon-o-arrow-path')
                ->color('primary')
                ->action(function () {
                   $data = OdooController::syncronizeDevis();
                   
                   foreach ($data['orders']  as $k => $devis) {
                    Devis::updateOrCreate([
                          'odoo_id' => $devis['id'],
                      ],
                      [
                          'odoo_id' => $devis['id'],
                          'customer_name' => $devis['customer_info'] ?? "",
                          'customer_id' => Customer::where('odoo_id', $devis['partner_id'][0] ?? null)->value('id'),
                          'generator_id' => Generator::where('odoo_id', $data['lines'][$k]['product_id'] ?? null)->value('id'),
                          'number' => $devis['name'],
                          'start_date' => $devis['date_order'],
                          'end_date' => $devis['expected_date'] == false ? null : $devis['expected_date'],
                          'forfait' => $devis['amount_total'],
                          'is_active' => $devis['invoice_status'] === 'no' ? true : false,
                          'user_id' => auth()->user()->id,
                      ]);
                   }
                })->requiresConfirmation(),
        ];
    }
}
