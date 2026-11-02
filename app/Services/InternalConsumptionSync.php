<?php

namespace App\Services;

use App\Enum\SynchronizationParametersType;
use App\Models\Generator;
use App\Models\InternalConsumption;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Import des consommations internes Odoo (mêmes filtres que la synchro « conso_interne »,
 * paramétrés dans SynchronizeParameter) et rattachement au GE par le n° de série écrit
 * dans les lignes de section de la commande, ex. :
 *   « - PARC LOCATION GROUPE ELECTROGENE / SDMO 44 kVA / TYPE GE : J44 / N° SERIE : 23 00 49 51 / 779 hrs »
 * Le n° de série d'un GE est le dernier mot de sa référence ou de son nom (« LOCJ44K 23004951 »).
 */
class InternalConsumptionSync
{
    public function __construct(private OdooService $odoo = new OdooService()) {}

    public function run(): array
    {
        set_time_limit(5000);

        $orders = $this->odoo->searchRead(
            'sale.order',
            DomainBuilder::build(SynchronizationParametersType::CONSO_INTERNE->value),
            ['name', 'state', 'date_order', 'amount_untaxed', 'order_line'],
        );

        $lineIds = collect($orders)->pluck('order_line')->flatten()->unique()->values()->all();
        $texts   = [];
        foreach (array_chunk($lineIds, 1000) as $chunk) {
            foreach ($this->odoo->searchRead('sale.order.line', [['id', 'in', $chunk]], ['order_id', 'name', 'display_type']) as $line) {
                $texts[$line['order_id'][0]][] = (string) $line['name'];
            }
        }

        $serials = $this->serialIndex();
        $matched = 0;
        $seen    = [];

        DB::transaction(function () use ($orders, $texts, $serials, &$matched, &$seen) {
            foreach ($orders as $order) {
                $text   = implode("\n", $texts[$order['id']] ?? []);
                $serial = $this->extractSerial($text);
                $generatorId = $serial ? ($serials[$serial] ?? $this->suffixMatch($serial, $serials)) : null;
                $matched += $generatorId ? 1 : 0;

                InternalConsumption::updateOrCreate(['odoo_order_id' => $order['id']], [
                    'generator_id'   => $generatorId,
                    'order_number'   => $order['name'],
                    'order_state'    => $order['state'] ?: null,
                    'order_date'     => $order['date_order'] ? Carbon::parse($order['date_order'], 'UTC')->setTimezone(config('app.timezone')) : null,
                    'amount_untaxed' => (float) ($order['amount_untaxed'] ?? 0),
                    'serial'         => $serial,
                    'hours'          => $this->extractHours($text),
                    'description'    => $this->extractDescription($text),
                ]);
                $seen[] = $order['id'];
            }

            InternalConsumption::query()->whereNotIn('odoo_order_id', $seen ?: [0])->delete();
        });

        return ['orders' => count($seen), 'matched' => $matched];
    }

    /** n° de série normalisé (sans espaces / tirets, majuscules) => generator_id */
    private function serialIndex(): array
    {
        $index = [];
        $generators = Generator::query()->without(['contractGenerator', 'etat', 'files'])->get(['id', 'reference', 'name', 'serial_number']);
        foreach ($generators as $g) {
            foreach ([$g->serial_number, $this->lastToken($g->reference), $this->lastToken($g->name)] as $candidate) {
                $key = $this->normalize($candidate);
                // Un vrai n° de série contient des chiffres et fait au moins 6 caractères
                if (strlen($key) >= 6 && preg_match('/\d/', $key)) {
                    $index[$key] ??= $g->id;
                }
            }
        }

        return $index;
    }

    /**
     * N° saisi collé au modèle (« R220C3 14007009 » → 14007009) : correspondance par fin de
     * numéro, seulement si elle est unique et porte sur au moins 8 caractères.
     */
    private function suffixMatch(string $serial, array $serials): ?int
    {
        $found = [];
        foreach ($serials as $key => $generatorId) {
            if (strlen($key) >= 8 && strlen($serial) >= 8 && (str_ends_with($serial, $key) || str_ends_with($key, $serial))) {
                $found[$generatorId] = true;
            }
        }

        return count($found) === 1 ? array_key_first($found) : null;
    }

    private function extractSerial(string $text): ?string
    {
        if (! preg_match('/N\s*[°ºo]?\s*(?:DE\s+)?S[ÉE]RIE\s*:?\s*([A-Z0-9][A-Z0-9 \-]{3,40})/iu', $text, $m)) {
            return null;
        }
        $key = $this->normalize($m[1]);

        return strlen($key) >= 4 ? $key : null;
    }

    private function extractHours(string $text): ?int
    {
        return preg_match('/(\d[\d \x{202F}]{0,8})\s*h(?:rs?|eures?)\b/iu', $text, $m)
            ? (int) preg_replace('/\D/', '', $m[1])
            : null;
    }

    /** Libellé de l'intervention : « - ENTRETIEN 250 HRS : » → « ENTRETIEN 250 HRS » */
    private function extractDescription(string $text): ?string
    {
        if (preg_match('/-\s*((?:INTERVENTION|ENTRETIEN|APPOINT|REMPLACEMENT|R[ÉE]PARATION|VIDANGE|D[ÉE]PANNAGE)[^\n:]*)/iu', $text, $m)) {
            return trim($m[1]);
        }

        return null;
    }

    private function lastToken(?string $value): ?string
    {
        if (! $value) {
            return null;
        }
        $parts = preg_split('/\s+/', trim(preg_replace('/\(.*?\)/', '', $value)));

        return end($parts) ?: null;
    }

    private function normalize(?string $value): string
    {
        return strtoupper(preg_replace('/[^A-Za-z0-9]/', '', (string) $value));
    }
}
