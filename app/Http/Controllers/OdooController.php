<?php

namespace App\Http\Controllers;

use App\Enum\DevisStats;
use App\Enum\GeneratorStatus;
use App\Enum\SynchronizationParametersType;
use App\Models\Customer;
use App\Models\Devis;
use App\Models\DevisGenerator;
use App\Models\Generator;
use App\Models\OperatorFilter;
use App\Models\Piece;
use App\Models\SynchronizeParameter;
use App\Models\Technicien;
use App\Services\DomainBuilder;
use App\Services\OdooService;

class OdooController extends Controller
{
    public function index(OdooService $odoo)
    {
        set_time_limit(5000);
        $companies = $odoo->getCompany();

        return $companies;
    }

    public static function syncronizeClient()
    {
        set_time_limit(5000);
        $odoo = new OdooService();
        $domain = DomainBuilder::build(SynchronizationParametersType::CUSTOMER->value);
        $data = $odoo->searchRead(
            'res.partner',
            $domain,
            [
                'id',
                'name',
                'phone',
                'email',
                'city',
            ]
        );


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
    }

    public static function syncronizeTechnicians()
    {
        set_time_limit(5000);
        $odoo = new OdooService();
        $domain = DomainBuilder::build(SynchronizationParametersType::TECHNICIEN->value);

        $data = $odoo->searchRead(
            'hr.employee',
            $domain,
            [
                'id',
                'name',
                'job_id',
            ]
        );

        foreach ($data as $tecnhnician) {
            Technicien::updateOrCreate(
                [
                    'odoo_id' => $tecnhnician['id']
                ],
                [
                    'odoo_id' => $tecnhnician['id'],
                    'name' => $tecnhnician['name'],
                    'job' => $tecnhnician['job_id'][1]
                ]
            );
        }
    }

    public static function syncronizeGenerator(bool $all = false)
    {
        set_time_limit(5000);

        $odoo      = new OdooService();
        $generator = Generator::all()->pluck('odoo_id')->toArray();
        $domain = DomainBuilder::build(SynchronizationParametersType::GENERATOR->value);
        if (!$all && !empty($generator)) {
            $domain[] = ['id', 'not in', $generator];
        }
        $data      = $odoo->searchRead(
            'product.template',
            $domain,
            [
                'id',
                'name',
                'default_code',
            ]
        );

        Generator::withoutEvents(function () use ($data) {
            foreach ($data as $product) {
                Generator::updateOrCreate(
                    [
                        'odoo_id' => $product['id']
                    ],
                    [
                        'odoo_id' => $product['id'],
                        'name' => $product['name'],
                        'type' => 1,
                        'reference' => $product['default_code'],
                    ]
                );
            }
        });

    }

    public static function syncronizeDevis(bool $all = false)
    {
        set_time_limit(5000);

        $odoo = new OdooService();
        $devis = Devis::all()->pluck('odoo_id')->toArray();
        $domain = DomainBuilder::build(SynchronizationParametersType::DEVIS->value);
        if (!$all && !empty($devis)) {
            $domain[] = ['id', 'not in', $devis];
        }
        $orders = $odoo->searchRead(
            'sale.order',
            $domain,
            [
                'id',
                'name',
                'partner_id',
                'customer_info',
                'order_line',
                'amount_total',
                'date_order',
                'invoice_status',
                'amount_total',
                'next_action_date',
                'state'
            ]
        );



        $allLineIds = [];

        foreach ($orders as $order) {
            $allLineIds = array_merge($allLineIds, $order['order_line'] ?? []);
        }

        $linesData = [];
        if (! empty($allLineIds)) {
            $linesData = $odoo->searchRead('sale.order.line', [
                (['id', 'in', $allLineIds]),
                (['is_rental', '=', true]),
            ], [
                'order_id',
                'product_template_id',
            ]);
        }


        Devis::withoutEvents(function () use ($orders) {
            foreach ($orders as $oder) {

                $customerId = Customer::where('odoo_id', $oder['partner_id'][0] ?? null)->value('id');
                if ($customerId != null) {
                    Devis::updateOrCreate(
                        [
                            'odoo_id' => $oder['id'],
                        ],
                        [
                            'odoo_id' => $oder['id'],
                            'customer_name' => $oder['customer_info'] ?? '',
                            'customer_id' => $customerId,
                            'number' => $oder['name'],
                            'start_date' => $oder['date_order'],
                            // 'end_date' => $oder['next_action_date'] == false ? null : $oder['next_action_date'],
                            'forfait' => $oder['amount_total'],
                            'is_active' => $oder['invoice_status'] === 'to invoice' ? true : false,
                            'state' => $oder['state'],
                            'is_conso_interne' => false,
                        ],
                    );
                }
            }
        });


        foreach ($linesData as $generator) {

            $generatorId = Generator::where('odoo_id', $generator['product_template_id'][0] ?? null)->value('id');

            $devis = Devis::where('odoo_id', $generator['order_id'][0] ?? null)->first();

            if (! $devis || ! $generatorId) {
                continue; // Skip si l’un des deux est manquant
            }

            $devisId = $devis->id;
            // $status = $devis->is_active;
            $status = $devis->state == DevisStats::DONE->value ? true : false;

            // $dataExiste = DevisGenerator::where('devis_id', $devisId)
            //     ->where(function ($query) use ($generatorId) {
            //         $query->where('generator_id', $generatorId)
            //             ->orWhere('old_generator_id', $generatorId);
            //     })
            //     ->first();

            // Correction de la logique avec where groupé
            $dataExiste = DevisGenerator::where('devis_id', $devisId)
                ->where(function ($query) use ($generatorId) {
                    $query->where('generator_id', $generatorId)
                        ->orWhere('old_generator_id', $generatorId);
                })
                ->first();

            if (optional(value: $dataExiste)->old_generator_id == null) {

                DevisGenerator::updateOrCreate(
                    [
                        'devis_id' => $devisId,
                        'generator_id' => $generatorId,
                    ],
                    [
                        'devis_id' => $devisId,
                        'generator_id' => $generatorId,
                        'status' => $status,
                    ]
                );

                // Met à jour le statut de tous les générateurs actifs
                // $generators = DevisGenerator::whereNotNull('generator_id')
                //     ->where('status', true)
                //     ->get();

                // foreach ($generators as $gen) {
                //     Generator::where('id', $gen->generator_id)
                //         ->update([
                //             'status' => GeneratorStatus::EN_LOCATION,
                //         ]);
                // }
            } else {
                //Juste une mise à jour du statut pour un ancien générateur
                DevisGenerator::where('devis_id', $devisId)
                    ->where('old_generator_id', $generatorId)
                    ->update([
                        'status' => $status,
                    ]);
            }
        }
    }

    public static function syncronizeConsoInterne()
    {
        set_time_limit(5000);

        $odoo = new OdooService();
        $domain = DomainBuilder::build(SynchronizationParametersType::CONSO_INTERNE->value);

        $orders = $odoo->searchRead(
            'sale.order',
            $domain,
            [
                'id',
                'name',
                'partner_id',
                'customer_info',
                'order_line',
                'amount_total',
                'date_order',
                'invoice_status',
                'amount_total',
                'next_action_date',
                'state'
            ]
        );


        foreach ($orders as $oder) {
            $customerId = Customer::where('odoo_id', $oder['partner_id'][0] ?? null)->value('id');

            if ($customerId != null) {
                Devis::updateOrCreate(
                    [
                        'odoo_id' => $oder['id'],
                    ],
                    [
                        'odoo_id' => $oder['id'],
                        'customer_name' => $oder['customer_info'] ?? '',
                        'customer_id' => $customerId,
                        'number' => $oder['name'],
                        'start_date' => $oder['date_order'],
                        'end_date' => $oder['next_action_date'] == false ? null : $oder['next_action_date'],
                        'forfait' => $oder['amount_total'],
                        'is_active' => $oder['invoice_status'] === 'to invoice' ? true : false,
                        'state' => $oder['state'],
                        'is_conso_interne' => true,
                    ],
                );
            }
        }
    }

    public static function syncronizePieces()
    {

        set_time_limit(5000);
        $odoo = new OdooService();
        $domain = DomainBuilder::build(SynchronizationParametersType::PIECE->value);

        $data = $odoo->searchRead(
            'product.template',
            $domain,
            [
                'id',
                'name',
                'standard_price',
                'list_price',
                'default_code',
            ]
        );



        foreach ($data as $piece) {
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
                ]
            );
        }
    }
}
