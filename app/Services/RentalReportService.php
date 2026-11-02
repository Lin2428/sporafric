<?php

namespace App\Services;

use App\Enum\GeneratorStatus;
use App\Enum\InterventionType;
use App\Enum\RentalUsage;
use App\Models\AppSetting;
use App\Models\Customer;
use App\Models\Generator;
use App\Models\InternalConsumption;
use App\Models\Intervention;
use App\Models\RentalPeriod;
use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Rapport de location : disponibilité, réservation, rentabilité et amortissement des GE,
 * + planning jour par jour (L / P / M / D) reconstitué depuis les périodes Odoo et les interventions.
 *
 * Règles :
 * - Seules les commandes confirmées/verrouillées comptent (les devis brouillons sont ignorés).
 * - Usage d'une période = client de la commande : clients dédiés PRÊT GE / MAINTENANCE GE /
 *   DOTATIONS - RP GE (paramétrables), sinon location.
 * - Jours de maintenance = périodes « MAINTENANCE GE » + jours d'intervention + consommations
 *   internes (InternalConsumption, 1 jour à la date de la commande), hors location/prêt.
 * - Coûts cumulés = interventions + pièces + consommations internes (HT).
 * - Un jour couvert par plusieurs usages prend le plus prioritaire (L > P > D > M).
 * - Une période couvre ses jours du départ (inclus) au retour (exclu) : 16→21 = 5 jours,
 *   comme la quantité facturée dans Odoo ; une période d'un seul jour compte 1 jour.
 */
class RentalReportService
{
    public const SETTING_COEFFICIENT = 'rental.coefficient';
    public const SETTING_CUSTOMERS   = 'rental.customers'; // ['P' => id, 'M' => id, 'D' => id]

    /** Au-delà, le planning passe en colonnes hebdomadaires. */
    public const MAX_DAY_COLUMNS = 400;

    /** Noms par défaut des clients dédiés (utilisés tant que le paramètre n'est pas renseigné). */
    private const DEFAULT_CUSTOMER_NAMES = [
        'P' => 'PRÊT GE',
        'M' => 'MAINTENANCE GE',
        'D' => 'DOTATIONS - RP GE',
    ];

    public static function coefficient(): float
    {
        return (float) AppSetting::get(self::SETTING_COEFFICIENT, 1.5);
    }

    /** @return array<int, RentalUsage> customer_id => usage (hors location) */
    public static function usageCustomers(): array
    {
        $configured = (array) AppSetting::get(self::SETTING_CUSTOMERS, []);
        $out = [];
        foreach (self::DEFAULT_CUSTOMER_NAMES as $code => $name) {
            $id = $configured[$code] ?? Customer::query()->where('name', $name)->value('id');
            if ($id) {
                $out[(int) $id] = RentalUsage::from($code);
            }
        }

        return $out;
    }

    /**
     * Prêts issus des interventions « Remplacement » sur un devis : le GE de remplacement (B,
     * new_generator_id) est PRÊTÉ, le GE remplacé (A, generator_id) est immobilisé (maintenance).
     * Fin du prêt = premier de : remplacement suivant sur le même devis qui retire B, retrait de B
     * sur ce devis, fin de la location Odoo du devis ; sinon en cours (aujourd'hui).
     *
     * @param  int[]  $generatorIds  GE concernés (prêtés ou remplacés)
     * @return Collection<int, array{loaned_id: int, replaced_id: int, devis_id: ?int, order_number: ?string, intervention: string, start: Carbon, end: Carbon, ongoing: bool}>
     */
    public static function replacementLoans(array $generatorIds): Collection
    {
        $base = fn () => Intervention::query()
            ->without(['interventionTechniciens', 'pieces', 'generator', 'newGenerator', 'fiches'])
            ->whereNotNull('devis_id')
            ->where(fn ($q) => $q->where('cancelled', false)->orWhereNull('cancelled'));

        $replacements = $base()
            ->where('type', InterventionType::REMPLACEMENT->value)
            ->whereNotNull('new_generator_id')
            ->where(fn ($q) => $q->whereIn('new_generator_id', $generatorIds)->orWhereIn('generator_id', $generatorIds))
            ->get(['id', 'numero', 'devis_id', 'generator_id', 'new_generator_id', 'start_date', 'date_planifiee']);
        if ($replacements->isEmpty()) {
            return collect();
        }

        $devisIds = $replacements->pluck('devis_id')->unique()->all();
        // Événements qui peuvent clore un prêt : remplacements / retraits sur les mêmes devis
        $closers = $base()
            ->whereIn('devis_id', $devisIds)
            ->whereIn('type', [InterventionType::REMPLACEMENT->value, InterventionType::RETRAIT->value])
            ->get(['id', 'type', 'devis_id', 'generator_id', 'start_date', 'end_date', 'date_planifiee']);
        $rentalEnds = RentalPeriod::query()->confirmed()->whereIn('devis_id', $devisIds)->get()
            ->groupBy('devis_id')->map(fn ($ps) => $ps->map(fn (RentalPeriod $p) => $p->effectiveEnd())->filter()->max());
        $orderNumbers = \App\Models\Devis::query()->withTrashed()->whereIn('id', $devisIds)->pluck('number', 'id');

        $dateOf = fn ($i) => $i->start_date ?? $i->date_planifiee;

        // GE « d'origine » de chaque devis (ligne de location Odoo) : les ramener = retour, pas prêt
        $originals = RentalPeriod::query()->whereIn('devis_id', $devisIds)->get(['devis_id', 'generator_id'])
            ->groupBy('devis_id')->map(fn ($ps) => $ps->pluck('generator_id')->map(fn ($id) => (int) $id)->all());

        return $replacements->map(function (Intervention $r) use ($closers, $rentalEnds, $orderNumbers, $dateOf, $originals) {
            $start = $dateOf($r) ? Carbon::parse($dateOf($r)) : null;
            if (! $start) {
                return null;
            }

            // Retour d'un GE déjà passé par ce devis (GE d'origine, ou remplacé plus tôt) : fin de prêt, pas un nouveau prêt
            $replacedBefore = $closers
                ->filter(fn ($c) => (string) $c->type === InterventionType::REMPLACEMENT->value && (int) $c->devis_id === (int) $r->devis_id
                    && $dateOf($c) && Carbon::parse($dateOf($c))->lt($start))
                ->pluck('generator_id')->map(fn ($id) => (int) $id)->all();
            if (in_array((int) $r->new_generator_id, [...($originals[$r->devis_id] ?? []), ...$replacedBefore], true)) {
                return null;
            }
            $ends = $closers
                ->filter(fn ($c) => (int) $c->id !== (int) $r->id && (int) $c->devis_id === (int) $r->devis_id
                    && (int) $c->generator_id === (int) $r->new_generator_id && $dateOf($c) && Carbon::parse($dateOf($c))->gt($start))
                ->map(fn ($c) => Carbon::parse((string) $c->type === InterventionType::RETRAIT->value ? ($c->end_date ?? $dateOf($c)) : $dateOf($c)));
            $rentalEnd = $rentalEnds[$r->devis_id] ?? null;
            if ($rentalEnd && $rentalEnd->gt($start)) {
                $ends->push($rentalEnd);
            }
            $end = $ends->min();

            return [
                'loaned_id'    => (int) $r->new_generator_id,
                'replaced_id'  => (int) $r->generator_id,
                'devis_id'     => $r->devis_id,
                'order_number' => $orderNumbers[$r->devis_id] ?? null,
                'intervention' => $r->numero ?: '#' . $r->id,
                'start'        => $start,
                'end'          => $end ? $end->min(now()) : now(),
                'ongoing'      => ! $end || $end->gt(now()),
            ];
        })->filter()->values();
    }

    /** GE de location sélectionnables dans le rapport. */
    public static function selectableGenerators(): Collection
    {
        return Generator::query()
            ->without(['contractGenerator', 'etat', 'files'])
            ->where('type', '1')
            ->orderBy('reference')
            ->get(['id', 'reference', 'name', 'power']);
    }

    /**
     * @param  int[]  $generatorIds
     * @return array{columns: array, rows: array, from: Carbon, to: Carbon, granularity: string, days: int}
     */
    public function build(array $generatorIds, Carbon $from, Carbon $to): array
    {
        $from = $from->copy()->startOfDay();
        $to   = $to->copy()->startOfDay();
        $days = $from->diffInDays($to) + 1;

        $generators = Generator::query()
            ->without(['etat', 'files'])
            ->with(['contractGenerator.contract.customer', 'devisGenerator.devis.customer'])
            ->whereIn('id', $generatorIds)
            ->orderBy('reference')
            ->get();

        $periods = RentalPeriod::query()
            ->confirmed()
            ->with('customer:id,name')
            ->whereIn('generator_id', $generatorIds)
            ->get()
            ->groupBy('generator_id');

        $interventions = Intervention::query()
            ->without(['interventionTechniciens', 'pieces', 'generator', 'newGenerator', 'fiches'])
            ->whereIn('generator_id', $generatorIds)
            ->where(fn ($q) => $q->where('cancelled', false)->orWhereNull('cancelled'))
            // Les remplacements sont traités comme des prêts (replacementLoans), pas comme des jours de réparation
            ->where(fn ($q) => $q->where('type', '<>', InterventionType::REMPLACEMENT->value)->orWhereNull('type'))
            ->get(['id', 'generator_id', 'numero', 'start_date', 'end_date', 'date_planifiee', 'status'])
            ->groupBy('generator_id');

        // Conso interne = maintenance (jour M + coût)
        $consumptions = InternalConsumption::query()
            ->confirmed()
            ->whereIn('generator_id', $generatorIds)
            ->get(['id', 'generator_id', 'order_number', 'order_date', 'amount_untaxed', 'description'])
            ->groupBy('generator_id');

        $costs = $this->cumulativeCosts($generatorIds);
        $loans = static::replacementLoans($generatorIds);

        $usageCustomers = static::usageCustomers();
        $coefficient    = static::coefficient();
        $granularity    = $days > self::MAX_DAY_COLUMNS ? 'week' : 'day';
        $columns        = $this->columns($from, $to, $granularity);

        $rows = $generators->map(function (Generator $generator) use ($periods, $interventions, $consumptions, $loans, $costs, $usageCustomers, $coefficient, $from, $to, $days, $columns, $granularity) {
            $gePeriods = $periods->get($generator->id, collect());
            $calendar  = $this->calendar($gePeriods, $interventions->get($generator->id, collect()), $consumptions->get($generator->id, collect()), $usageCustomers, $from, $to);
            $this->applyLoans($calendar, $generator->id, $loans, $from, $to);

            $count = fn (RentalUsage $u) => collect($calendar)->where('code', $u->value)->count();
            $l = $count(RentalUsage::LOCATION);
            $p = $count(RentalUsage::PRET);
            $m = $count(RentalUsage::MAINTENANCE);
            $d = $count(RentalUsage::DOTATION);
            $available = $days - $m;

            // Financier (cumulé depuis l'origine, locations facturables uniquement)
            $locationPeriods = $gePeriods->filter(fn (RentalPeriod $rp) => $this->usageOf($rp, $usageCustomers) === RentalUsage::LOCATION);
            $revenue  = (float) $locationPeriods->filter(fn ($rp) => ($rp->effectiveStart() ?? now()) <= now())->sum('amount_untaxed');
            $revenueInRange = $this->revenueInRange($locationPeriods, $from, $to);
            $cost     = (float) ($costs[$generator->id] ?? 0);
            $pr       = $generator->pr !== null ? (float) $generator->pr : null;
            $hours    = is_numeric($generator->houres) ? (int) $generator->houres : null;
            $margin   = $revenue - $cost;
            $threshold = $pr ? $pr * $coefficient : null;

            $ongoing  = $gePeriods->first(fn (RentalPeriod $rp) => $rp->isOngoing());
            $ongoingLoan = $loans->first(fn ($l) => $l['ongoing'] && $l['loaned_id'] === $generator->id);
            $inRange  = $gePeriods->filter(fn (RentalPeriod $rp) => $this->overlaps($rp, $from, $to))
                ->sortBy(fn ($rp) => $rp->effectiveStart());

            return [
                'id'               => $generator->id,
                'reference'        => $generator->reference,
                'name'             => $generator->name,
                'kva'              => $generator->power !== null ? (float) $generator->power : null,
                'status'           => GeneratorStatus::tryFrom((string) $generator->status)?->label(),
                'status_colors'    => GeneratorStatus::tryFrom((string) $generator->status)?->colors(),
                'hours'            => $hours,
                'days_location'    => $l,
                'days_pret'        => $p,
                'days_maintenance' => $m,
                'days_dotation'    => $d,
                'occupation'       => $days ? round(($l + $p) / $days * 100, 1) : null,
                'availability'     => $days ? round($available / $days * 100, 1) : null,
                'booking'          => $available > 0 ? round(($l + $p) / $available * 100, 1) : null,

                'customer'         => $this->currentCustomer($generator, $ongoing),
                'site'             => $generator->devisGenerator?->site ?? $generator->contractGenerator?->site,
                'commissioning'    => $generator->{'start-up'},
                'next_vidange'     => $generator->next_vidange !== null ? (int) $generator->next_vidange : null,

                'pr'               => $pr,
                'threshold'        => $threshold,
                'revenue_range'    => round($revenueInRange),
                'revenue'          => round($revenue),
                'costs'            => round($cost),
                'margin'           => round($margin),
                'profitability'    => $pr ? round($margin / $pr * 100, 1) : null,
                'remaining'        => $threshold !== null ? round(max(0, $threshold - $margin)) : null,
                'amortized_hours'  => ($threshold !== null && $margin > 0 && $hours) ? (int) round($threshold / ($margin / $hours)) : null,
                'financial_counter' => $threshold !== null ? round(max(0, $margin - $threshold)) : null,
                'rental_status'    => match (true) {
                    $ongoing !== null     => 'En cours : ' . $ongoing->order_number . ' depuis le ' . $ongoing->effectiveStart()?->format('d/m/Y'),
                    $ongoingLoan !== null => 'Prêt en cours : ' . ($ongoingLoan['order_number'] ?? $ongoingLoan['intervention']) . ' depuis le ' . $ongoingLoan['start']->format('d/m/Y'),
                    default               => null,
                },

                'orders'           => $inRange->map(fn (RentalPeriod $rp) => $rp->order_number
                    . ' (' . ($rp->invoice_numbers ?: ($rp->invoice_status === 'invoiced' ? 'facturé' : 'à facturer')) . ')')
                    ->unique()->implode(', '),
                'cells'            => $this->cells($calendar, $columns, $granularity),
            ];
        })->values()->all();

        return compact('columns', 'rows', 'from', 'to', 'granularity', 'days');
    }

    /**
     * Usage jour par jour : ['Y-m-d' => ['code' => 'L', 'refs' => ['DEVIS/…']]].
     */
    private function calendar(Collection $periods, Collection $interventions, Collection $consumptions, array $usageCustomers, Carbon $from, Carbon $to): array
    {
        $calendar = [];
        $mark = function (string $day, RentalUsage $usage, string $ref) use (&$calendar) {
            $current = isset($calendar[$day]) ? RentalUsage::from($calendar[$day]['code']) : null;
            if (! $current || $usage->priority() > $current->priority()) {
                $calendar[$day] = ['code' => $usage->value, 'refs' => [$ref]];
            } elseif ($usage === $current && ! in_array($ref, $calendar[$day]['refs'], true)) {
                $calendar[$day]['refs'][] = $ref;
            }
        };

        foreach ($periods as $period) {
            $start = $period->effectiveStart()?->copy()->startOfDay();
            $end   = $period->effectiveEnd()?->copy()->startOfDay();
            if (! $start || ! $end || $end < $start) {
                continue;
            }
            // Retour exclu, sauf période d'un seul jour
            $last = $end->gt($start) ? $end->copy()->subDay() : $start;
            foreach ($this->clip($start, $last, $from, $to) as $day) {
                $mark($day, $this->usageOf($period, $usageCustomers), $period->order_number);
            }
        }

        foreach ($interventions as $intervention) {
            $start = Carbon::parse($intervention->start_date ?? $intervention->date_planifiee)->startOfDay();
            $end   = $intervention->end_date
                ? Carbon::parse($intervention->end_date)->startOfDay()
                : ((string) $intervention->status === '1' ? now()->startOfDay() : $start); // en cours → jusqu'à aujourd'hui
            foreach ($this->clip($start, $end->max($start), $from, $to) as $day) {
                $mark($day, RentalUsage::MAINTENANCE, 'Intervention ' . ($intervention->numero ?: '#' . $intervention->id));
            }
        }

        foreach ($consumptions as $consumption) {
            if (! $consumption->order_date) {
                continue;
            }
            $day = $consumption->order_date->copy()->startOfDay();
            foreach ($this->clip($day, $day, $from, $to) as $d) {
                $mark($d, RentalUsage::MAINTENANCE, 'Conso interne ' . $consumption->order_number . ($consumption->description ? ' — ' . $consumption->description : ''));
            }
        }

        return $calendar;
    }

    /**
     * Remplacements : jours de prêt (P) pour le GE de remplacement ; pour le GE remplacé, les jours
     * de location de ce devis deviennent de l'immobilisation (M) — il est rentré en révision.
     */
    private function applyLoans(array &$calendar, int $generatorId, Collection $loans, Carbon $from, Carbon $to): void
    {
        foreach ($loans as $loan) {
            $start = $loan['start']->copy()->startOfDay();
            $end   = $loan['end']->copy()->startOfDay();
            $last  = $end->gt($start) ? $end->copy()->subDay() : $start;

            if ($loan['loaned_id'] === $generatorId) {
                $ref = 'Prêt (remplacement ' . $loan['intervention'] . ($loan['order_number'] ? ' sur ' . $loan['order_number'] : '') . ')';
                foreach ($this->clip($start, $last, $from, $to) as $day) {
                    $current = isset($calendar[$day]) ? RentalUsage::from($calendar[$day]['code']) : null;
                    if (! $current || RentalUsage::PRET->priority() > $current->priority()) {
                        $calendar[$day] = ['code' => RentalUsage::PRET->value, 'refs' => [$ref]];
                    }
                }
            }

            if ($loan['replaced_id'] === $generatorId) {
                // Le GE remplacé est physiquement à l'atelier : M l'emporte sur ses lignes de location
                // (souvent plusieurs commandes qui se chevauchent : prolongations).
                $ref = 'Remplacé (' . $loan['intervention'] . ') — en révision';
                foreach ($this->clip($start, $last, $from, $to) as $day) {
                    $entry = $calendar[$day] ?? null;
                    if ($entry && $entry['code'] === RentalUsage::MAINTENANCE->value) {
                        if (! in_array($ref, $entry['refs'], true)) {
                            $calendar[$day]['refs'][] = $ref;
                        }
                    } else {
                        $calendar[$day] = ['code' => RentalUsage::MAINTENANCE->value, 'refs' => [$ref]];
                    }
                }
            }
        }
    }

    /** @return string[] jours (Y-m-d) de [start, end] compris dans [from, to] */
    private function clip(Carbon $start, Carbon $end, Carbon $from, Carbon $to): array
    {
        $a = $start->max($from);
        $b = $end->min($to);
        if ($a > $b) {
            return [];
        }

        return collect(CarbonPeriod::create($a, $b))->map(fn ($d) => $d->toDateString())->all();
    }

    private function usageOf(RentalPeriod $period, array $usageCustomers): RentalUsage
    {
        return $usageCustomers[$period->customer_id] ?? RentalUsage::LOCATION;
    }

    private function overlaps(RentalPeriod $period, Carbon $from, Carbon $to): bool
    {
        $start = $period->effectiveStart();
        $end   = $period->effectiveEnd();

        return $start && $end && $start->copy()->startOfDay() <= $to && $end->copy()->startOfDay() >= $from;
    }

    /** CA HT des locations au prorata des jours compris dans la période. */
    private function revenueInRange(Collection $periods, Carbon $from, Carbon $to): float
    {
        return (float) $periods->sum(function (RentalPeriod $period) use ($from, $to) {
            $start = $period->effectiveStart()?->copy()->startOfDay();
            $end   = $period->effectiveEnd()?->copy()->startOfDay();
            if (! $start || ! $end) {
                return 0;
            }
            $total = max(1, $start->diffInDays($end));
            $last  = $end->gt($start) ? $end->copy()->subDay() : $start;
            $in    = count($this->clip($start, $last, $from, $to));

            return $period->amount_untaxed * $in / $total;
        });
    }

    /** Coûts cumulés par GE : montant des interventions + pièces consommées (qté × prix). */
    private function cumulativeCosts(array $generatorIds): array
    {
        $interventions = DB::table('interventions')
            ->selectRaw('generator_id, SUM(COALESCE(montant, 0)) AS total')
            ->whereIn('generator_id', $generatorIds)
            ->whereNull('deleted_at')
            ->groupBy('generator_id')
            ->pluck('total', 'generator_id');

        $pieces = DB::table('intervention_pieces as ip')
            ->join('interventions as i', 'i.id', '=', 'ip.intervention_id')
            ->selectRaw('i.generator_id, SUM(COALESCE(ip.qty, 0) * COALESCE(ip.price, 0)) AS total')
            ->whereIn('i.generator_id', $generatorIds)
            ->whereNull('i.deleted_at')
            ->groupBy('i.generator_id')
            ->pluck('total', 'generator_id');

        $consumptions = InternalConsumption::query()
            ->confirmed()
            ->selectRaw('generator_id, SUM(amount_untaxed) AS total')
            ->whereIn('generator_id', $generatorIds)
            ->groupBy('generator_id')
            ->pluck('total', 'generator_id');

        $out = [];
        foreach ($generatorIds as $id) {
            $out[$id] = (float) ($interventions[$id] ?? 0) + (float) ($pieces[$id] ?? 0) + (float) ($consumptions[$id] ?? 0);
        }

        return $out;
    }

    private function currentCustomer(Generator $generator, ?RentalPeriod $ongoing): ?string
    {
        return $ongoing?->customer?->name
            ?? $generator->devisGenerator?->devis?->customer?->name
            ?? $generator->contractGenerator?->contract?->customer?->name;
    }

    /**
     * Colonnes du planning : jours, ou semaines au-delà de MAX_DAY_COLUMNS.
     * @return array<int, array{key: string, label: string, month: string, start: string, end: string}>
     */
    private function columns(Carbon $from, Carbon $to, string $granularity): array
    {
        $out = [];
        if ($granularity === 'day') {
            foreach (CarbonPeriod::create($from, $to) as $day) {
                $out[] = [
                    'key'   => $day->toDateString(),
                    'label' => $day->format('d'),
                    'month' => ucfirst($day->translatedFormat('M Y')),
                    'start' => $day->toDateString(),
                    'end'   => $day->toDateString(),
                ];
            }

            return $out;
        }

        $cursor = $from->copy()->startOfWeek();
        while ($cursor <= $to) {
            $end = $cursor->copy()->endOfWeek()->startOfDay();
            $out[] = [
                'key'   => $cursor->toDateString(),
                'label' => 'S' . $cursor->isoWeek(),
                'month' => ucfirst($cursor->translatedFormat('M Y')),
                'start' => $cursor->max($from)->toDateString(),
                'end'   => $end->min($to)->toDateString(),
            ];
            $cursor->addWeek();
        }

        return $out;
    }

    /**
     * Une cellule par colonne : code dominant + références (commandes / interventions).
     * @return array<int, ?array{code: string, refs: string[], days: int}>
     */
    private function cells(array $calendar, array $columns, string $granularity): array
    {
        return array_map(function (array $column) use ($calendar, $granularity) {
            if ($granularity === 'day') {
                return isset($calendar[$column['key']]) ? $calendar[$column['key']] + ['days' => 1] : null;
            }

            $codes = [];
            $refs  = [];
            foreach (CarbonPeriod::create($column['start'], $column['end']) as $day) {
                $entry = $calendar[$day->toDateString()] ?? null;
                if ($entry) {
                    $codes[$entry['code']] = ($codes[$entry['code']] ?? 0) + 1;
                    $refs = array_merge($refs, $entry['refs']);
                }
            }
            if (! $codes) {
                return null;
            }
            // Usage le plus fréquent de la semaine, priorité en cas d'égalité
            uksort($codes, fn ($a, $b) => [$codes[$b], RentalUsage::from($b)->priority()] <=> [$codes[$a], RentalUsage::from($a)->priority()]);

            return ['code' => array_key_first($codes), 'refs' => array_values(array_unique($refs)), 'days' => array_sum($codes)];
        }, $columns);
    }
}
