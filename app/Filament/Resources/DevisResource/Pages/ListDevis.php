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
    public array $devis;
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
                ->beforeFormFilled(function () {
                    $this->devis = [];
                })
                ->action(function () {
                     set_time_limit(120);
                     try {
                            $this->devis = OdooController::syncronizeDevis($this->category);  
                        } catch (\Throwable $th) {
                            Notification::make()
                            ->title('Une erreur est survenue lors de la synchronisation !')
                            ->danger()
                            ->send();

                            return;
                        }             

                    foreach ($this->devis['orders'] as $k => $devis) {
                        $customerId = Customer::where('odoo_id', $devis['partner_id'][0] ?? null)->value('id');
                        Devis::updateOrCreate(
                            [
                                'odoo_id' => $devis['id'],
                            ],
                            [
                                'odoo_id' => $devis['id'],
                                'customer_name' => $devis['customer_info'] ?? '',
                                'customer_id' => $customerId,
                                'number' => $devis['name'],
                                'start_date' => $devis['date_order'],
                                'end_date' => $devis['expected_date'] == false ? null : $devis['expected_date'],
                                'forfait' => $devis['amount_total'],
                                'is_active' => $devis['invoice_status'] === 'no' ? true : false,
                                'state' => $devis['state'],
                                'user_id' => auth()->user()->id,
                            ],
                        );
                    }

                    foreach ($this->devis['lines'] as $generator) {
                        $generatorId = Generator::where('odoo_id', $generator['product_id'][0] ?? null)->value('id');
                        $devisId = Devis::where('odoo_id', $generator['order_id'][0] ?? null)->value('id');
                        $status = Devis::where('odoo_id', $generator['order_id'][0] ?? null)->value('is_active');

                        if ($generatorId != null) {
                            DevisGenerator::updateOrCreate(
                                [
                                    'devis_id' => $devisId,
                                    'generator_id' => $generatorId,
                                ],
                                [
                                    'devis_id' => $devisId,
                                    'generator_id' => $generatorId,
                                    'status' => $status,
                                ],
                            );
                        }
                    }

                    $generators = DevisGenerator::where('generator_id', '!=', null)->where('status', true)->get();

                    foreach ($generators as $gen) {
                        Generator::where('id', $gen->generator_id)->update([
                            'status' => GeneratorStatus::EN_LOCATION,
                        ]);
                    }
                    Notification::make()->title('Synchronisation terminée')->body('Les devis ont été synchronisés avec succès.')->success()->send();
                })
                ->visible(auth()->user()->hasPermissionTo('create_devis')),
        ];
    }

    public function getProductViewField(): ViewField
    {
        return ViewField::make('devis_table')->view('filament.devis.devis-table');
    }
}
