<?php
namespace App\Http\Controllers;

use App\Models\Generator;
use App\Services\OdooService;

class OdooController extends Controller
{
    public function index(OdooService $odoo)
    {
        $companies = $odoo->getCompany();

        return $companies;
    }

    public static function syncronizeClient()
    {
        $odoo = new OdooService();
        $data = $odoo->searchRead('res.partner',
            [
                (['is_company', '=', true]),
            ],
            [
                'id',
                'name',
                'phone',
                'email',
                'city',
            ]);

        return $data;
    }

    public static function syncronizeTechnicians()
    {
        $odoo = new OdooService();
        $data = $odoo->searchRead('hr.employee',
            [
                (['department_id', '=', 6]),
            ],
            [
                'id',
                'name',
            ]);

        return $data;
    }

    public static function syncronizeGenerator(bool $all = false)
    {
        $odoo      = new OdooService();
        $generator = Generator::all()->pluck('odoo_id')->toArray();
        $data      = $odoo->searchRead('product.template',
            $all ? [

                (['categ_id', 'in', [82, 240]]),
                (['active', '=', true]),
            ] :
            [
                (['id', 'not in', $generator]),
                (['categ_id', 'in', [82, 240]]),
                (['active', '=', true]),
            ],
            [
                'id',
                'name',
                'default_code',
            ]);

        return $data;
    }

    public static function syncronizeDevis()
    {
        $odoo = new OdooService();

        $orders = $odoo->searchRead('sale.order', [
            ['is_rental_order', '=', true],
        ], [
            'id',
            'name',
            'partner_id',
            'order_line',
            'amount_total',
            'date_order',
            'invoice_status',
        ]);

        $allLineIds = [];

        foreach ($orders as $order) {
            $allLineIds = array_merge($allLineIds, $order['order_line'] ?? []);
        }

        $linesData = [];
        if (! empty($allLineIds)) {
            $linesData = $odoo->searchRead('sale.order.line', [
                ['id', 'in', $allLineIds],
            ], [
                'order_id',
                'product_id',
            ]);
        }

        return [
            'orders' => $orders,
            'lines'  => $linesData,
        ];
    }

    public static function syncronizePieces()
    {
        $odoo = new OdooService();

        $orders = $odoo->searchRead('product.template', [
            ['categ_id', '=', 239],
        ], [
            'id',
            'name',
            'standard_price',
            'list_price',
            'default_code',
        ]);

        return $orders;
    }
}
