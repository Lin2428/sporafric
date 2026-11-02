<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Devis;
use App\Models\Generator;
use App\Models\RentalPeriod;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Import des périodes de location depuis Odoo 16 (module Location).
 *
 * - Ligne `sale.order.line` is_rental → dates prévues (start_date / return_date) + montant HT.
 * - Dates RÉELLES : pas de bon de transfert dans cette config, Odoo crée des `stock.move`
 *   « Mouvement de location » sans picking : Stock → Rental = départ, Rental → Stock = retour.
 * - Le prix de revient du GE (`generators.pr`) n'est PAS importé : saisi dans la fiche GE.
 */
class RentalPeriodSync
{
    public function __construct(private OdooService $odoo = new OdooService()) {}

    public function run(): int
    {
        set_time_limit(5000);

        $generatorIds = Generator::query()->withTrashed()->whereNotNull('odoo_id')->pluck('id', 'odoo_id');
        if ($generatorIds->isEmpty()) {
            return 0;
        }

        $lines = $this->odoo->searchRead('sale.order.line', [
            ['is_rental', '=', true],
            ['product_template_id', 'in', $generatorIds->keys()->map(fn ($id) => (int) $id)->values()->all()],
        ], ['order_id', 'product_template_id', 'start_date', 'return_date', 'price_subtotal', 'invoice_status']);

        $orders   = $this->ordersById(collect($lines)->pluck('order_id.0')->unique()->values()->all());
        $invoices = $this->invoiceNames(collect($orders)->pluck('invoice_ids')->flatten()->unique()->values()->all());
        $moves    = $this->actualDates(collect($lines)->pluck('id')->all());

        $devisIds    = Devis::query()->withTrashed()->whereNotNull('odoo_id')->pluck('id', 'odoo_id');
        $customerIds = Customer::query()->whereNotNull('odoo_id')->pluck('id', 'odoo_id');

        $seen = [];
        DB::transaction(function () use ($lines, $orders, $invoices, $moves, $generatorIds, $devisIds, $customerIds, &$seen) {
            foreach ($lines as $line) {
                $generatorId = $generatorIds[$line['product_template_id'][0] ?? 0] ?? null;
                $order       = $orders[$line['order_id'][0] ?? 0] ?? null;
                if (! $generatorId || ! $order) {
                    continue;
                }

                $invoiceNames = collect($order['invoice_ids'] ?? [])->map(fn ($id) => $invoices[$id] ?? null)->filter()->implode(', ');

                RentalPeriod::updateOrCreate(['odoo_line_id' => $line['id']], [
                    'odoo_order_id'   => $order['id'],
                    'generator_id'    => $generatorId,
                    'devis_id'        => $devisIds[$order['id']] ?? null,
                    'customer_id'     => $customerIds[$order['partner_id'][0] ?? 0] ?? null,
                    'order_number'    => $order['name'],
                    'order_state'     => $order['state'] ?: null,
                    'rental_status'   => $order['rental_status'] ?: null,
                    'planned_start'   => $this->toLocal($line['start_date'] ?? null),
                    'planned_return'  => $this->toLocal($line['return_date'] ?? null),
                    'picked_up_at'    => $this->toLocal($moves[$line['id']]['pickup'] ?? null),
                    'returned_at'     => $this->toLocal($moves[$line['id']]['return'] ?? null),
                    'amount_untaxed'  => (float) ($line['price_subtotal'] ?? 0),
                    'invoice_status'  => $line['invoice_status'] ?: null,
                    'invoice_numbers' => $invoiceNames ?: null,
                ]);
                $seen[] = $line['id'];
            }

            // Lignes supprimées dans Odoo
            RentalPeriod::query()->whereNotIn('odoo_line_id', $seen ?: [0])->delete();
        });

        return count($seen);
    }

    /** @return array<int, array> sale.order indexés par id */
    private function ordersById(array $orderIds): array
    {
        $out = [];
        foreach (array_chunk($orderIds, 500) as $chunk) {
            foreach ($this->odoo->searchRead('sale.order', [['id', 'in', $chunk]], ['name', 'partner_id', 'state', 'rental_status', 'invoice_ids']) as $o) {
                $out[$o['id']] = $o;
            }
        }

        return $out;
    }

    /** @return array<int, string> numéros des factures clients validées, indexés par id */
    private function invoiceNames(array $invoiceIds): array
    {
        $out = [];
        foreach (array_chunk($invoiceIds, 500) as $chunk) {
            $rows = $this->odoo->searchRead('account.move', [
                ['id', 'in', $chunk],
                ['state', '=', 'posted'],
                ['move_type', '=', 'out_invoice'],
            ], ['name']);
            foreach ($rows as $m) {
                $out[$m['id']] = $m['name'];
            }
        }

        return $out;
    }

    /**
     * Dates réelles de départ / retour par ligne de commande, d'après les mouvements de stock
     * terminés entrant / sortant de l'emplacement « Rental ».
     *
     * @return array<int, array{pickup: ?string, return: ?string}>
     */
    private function actualDates(array $lineIds): array
    {
        $rentalLocations = collect($this->odoo->searchRead('stock.location', [['name', '=', 'Rental']], ['id']))->pluck('id')->all();
        if (empty($rentalLocations) || empty($lineIds)) {
            return [];
        }

        $out = [];
        foreach (array_chunk($lineIds, 500) as $chunk) {
            $moves = $this->odoo->searchRead('stock.move', [
                ['sale_line_id', 'in', $chunk],
                ['state', '=', 'done'],
            ], ['sale_line_id', 'date', 'location_id', 'location_dest_id']);

            foreach ($moves as $move) {
                $lineId = $move['sale_line_id'][0] ?? null;
                if (! $lineId) {
                    continue;
                }
                $out[$lineId] ??= ['pickup' => null, 'return' => null];

                if (in_array($move['location_dest_id'][0] ?? null, $rentalLocations, true)) {
                    // Premier départ
                    $out[$lineId]['pickup'] = min(array_filter([$out[$lineId]['pickup'], $move['date']]));
                } elseif (in_array($move['location_id'][0] ?? null, $rentalLocations, true)) {
                    // Dernier retour
                    $out[$lineId]['return'] = max(array_filter([$out[$lineId]['return'], $move['date']]));
                }
            }
        }

        return $out;
    }

    /** Odoo renvoie des datetimes UTC « Y-m-d H:i:s » (ou false). */
    private function toLocal(mixed $value): ?Carbon
    {
        if (! $value) {
            return null;
        }

        return Carbon::parse($value, 'UTC')->setTimezone(config('app.timezone'));
    }
}
