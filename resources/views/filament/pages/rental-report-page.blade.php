@php
    use App\Enum\RentalUsage;
    use Illuminate\Support\Carbon;

    $report = $this->report;
    $groups = $this->columns();

    $fmt = function ($value, string $type) {
        if ($value === null || $value === '') {
            return '—';
        }
        return match ($type) {
            'int'   => number_format((float) $value, 0, ',', ' '),
            'dec'   => rtrim(rtrim(number_format((float) $value, 2, ',', ' '), '0'), ','),
            'pct'   => number_format((float) $value, 1, ',', ' ') . ' %',
            'money' => number_format((float) $value, 0, ',', ' '),
            'date'  => $value instanceof \DateTimeInterface ? $value->format('d/m/Y') : Carbon::parse($value)->format('d/m/Y'),
            default => $value,
        };
    };
@endphp

<x-filament-panels::page>
    <style>
        .rr-wrap { overflow: auto; max-height: 75vh; border: 1px solid #e5e7eb; border-radius: 10px; background: #fff; }
        .rr-table { border-collapse: separate; border-spacing: 0; font-size: 12px; }
        .rr-table th, .rr-table td { border-right: 1px solid #e5e7eb; border-bottom: 1px solid #e5e7eb; padding: 4px 8px; white-space: nowrap; background: #fff; color: #1f2937; }
        .rr-table thead th { position: sticky; z-index: 2; background: #f8fafc; font-weight: 600; text-align: left; }
        .rr-table thead tr:first-child th { top: 0; height: 26px; }
        .rr-table thead tr:nth-child(2) th { top: 26px; }
        .rr-table .rr-group { text-align: center; text-transform: uppercase; font-size: 10px; letter-spacing: .05em; color: #64748b; }
        .rr-table .rr-sticky { position: sticky; left: 0; z-index: 1; width: 140px; min-width: 140px; max-width: 140px; overflow: hidden; text-overflow: ellipsis; }
        .rr-table .rr-sticky-2 { position: sticky; left: 140px; z-index: 1; min-width: 200px; max-width: 240px; overflow: hidden; text-overflow: ellipsis; box-shadow: 2px 0 0 #cbd5e1; }
        .rr-table thead .rr-sticky, .rr-table thead .rr-sticky-2 { z-index: 3; }
        .rr-table .rr-num { text-align: right; font-variant-numeric: tabular-nums; }
        .rr-table .rr-orders { max-width: 360px; overflow: hidden; text-overflow: ellipsis; }
        .rr-table .pc { width: 18px; min-width: 18px; padding: 0; text-align: center; font-size: 9px; font-weight: 700; cursor: default; }
        .rr-table .pc[data-r] { cursor: pointer; }
        .rr-table th.pc { font-weight: 500; color: #64748b; }
        .rr-table .rr-month { text-align: left; border-left: 2px solid #cbd5e1; }
        .rr-help { display: inline-flex; align-items: center; justify-content: center; width: 15px; height: 15px; margin-left: 4px; border-radius: 9999px; background: #e2e8f0; color: #475569; font-size: 10px; font-weight: 700; cursor: help; }
        .rr-badge { display: inline-block; padding: 1px 8px; border-radius: 9999px; font-size: 11px; font-weight: 600; line-height: 18px; }
        .rr-legend { display: flex; flex-wrap: wrap; gap: 12px; font-size: 12px; align-items: center; }
        .rr-chip { display: inline-flex; width: 18px; height: 18px; border-radius: 4px; align-items: center; justify-content: center; font-size: 10px; font-weight: 700; margin-right: 4px; }
        .rr-detail { border: 1px solid #fde68a; background: #fffbeb; border-radius: 10px; padding: 10px 14px; font-size: 13px; color: #1f2937; }
        {{-- Pas de @foreach ici : Livewire insère des commentaires HTML dans les boucles, ce qui casse le CSS --}}
        {!! collect(RentalUsage::cases())->map(fn ($u) => ".u-{$u->value} { background: {$u->colors()[0]} !important; color: {$u->colors()[1]} !important; }")->implode("\n") !!}
        .dark .rr-wrap { background: #18181b; border-color: #3f3f46; }
        .dark .rr-table th, .dark .rr-table td { background: #18181b; color: #e4e4e7; border-color: #3f3f46; }
        .dark .rr-table thead th { background: #27272a; }
        .dark .rr-help { background: #3f3f46; color: #e4e4e7; }
        .dark .rr-detail { background: #422006; border-color: #854d0e; color: #fef3c7; }
    </style>

    <form wire:submit="generate" class="space-y-4">
        {{ $this->form }}
        <x-filament::button type="submit" icon="heroicon-o-play" wire:loading.attr="disabled">
            <span wire:loading.remove wire:target="generate">Générer le rapport</span>
            <span wire:loading wire:target="generate">Calcul en cours…</span>
        </x-filament::button>
    </form>

    @if ($report)
        @php
            // Début de mois (bordure) et libellés de colonnes pour le JS
            $monthStarts = collect($report['columns'])->map(fn ($c, $i) => $i === 0 || $c['month'] !== $report['columns'][$i - 1]['month'])->all();
            $colLabels = collect($report['columns'])->map(fn ($c) => Carbon::parse($c['start'])->format('d/m/Y') . ($c['start'] !== $c['end'] ? ' au ' . Carbon::parse($c['end'])->format('d/m/Y') : ''))->all();
            $usageLabels = collect(RentalUsage::cases())->mapWithKeys(fn ($u) => [$u->value => $u->label()])->all();
        @endphp
        <div x-data="{
                sel: null,
                cols: @js($colLabels),
                usages: @js($usageLabels),
                describe(c) {
                    const code = [...c.classList].find(k => k.startsWith('u-')).slice(2);
                    const n = c.dataset.n ? ` (${c.dataset.n} j)` : '';
                    return `${c.closest('tr').dataset.ge} — ${this.cols[c.dataset.c]} — ${this.usages[code]}${n}`;
                },
             }" class="space-y-3">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <p class="text-sm text-gray-600 dark:text-gray-300">
                    {{ count($report['rows']) }} groupe(s) — du {{ $report['from']->format('d/m/Y') }} au {{ $report['to']->format('d/m/Y') }}
                    ({{ $report['days'] }} jours calendaires){{ $report['granularity'] === 'week' ? ' — planning par semaine' : '' }}
                </p>
                <div class="rr-legend">
                    @foreach (RentalUsage::cases() as $u)
                        <span><span class="rr-chip u-{{ $u->value }}">{{ $u->value }}</span>{{ $u->label() }}</span>
                    @endforeach
                    <span class="text-gray-500">Cliquez sur une case pour voir les commandes / BL.</span>
                </div>
            </div>

            <template x-if="sel">
                <div class="rr-detail flex items-start justify-between gap-3">
                    <div>
                        <p class="font-semibold" x-text="sel.title"></p>
                        <ul class="mt-1 list-disc pl-5">
                            <template x-for="r in sel.refs" :key="r"><li x-text="r"></li></template>
                        </ul>
                    </div>
                    <button type="button" class="text-gray-500" x-on:click="sel = null">✕</button>
                </div>
            </template>

            @if (empty($report['rows']))
                <p class="text-sm text-gray-500">Aucun groupe sélectionné.</p>
            @else
            <div class="rr-wrap">
                <table class="rr-table"
                       x-on:mouseover="const c = $event.target.closest('td[data-r]'); if (c && ! c.title) c.title = describe(c) + '\n' + c.dataset.r.split('|').join(', ')"
                       x-on:click="const c = $event.target.closest('td[data-r]'); if (c) sel = { title: describe(c), refs: c.dataset.r.split('|') }">
                    <thead>
                        <tr>
                            @foreach ($groups as $group => $cols)
                                @if ($loop->first)
                                    {{-- ID GE + Désignation figés : leur en-tête de groupe l'est aussi --}}
                                    <th class="rr-group rr-sticky"></th>
                                    <th class="rr-group rr-sticky-2" style="text-align: left;">{{ $group }}</th>
                                    <th colspan="{{ count($cols) - 2 }}" class="rr-group"></th>
                                @else
                                    <th colspan="{{ count($cols) }}" class="rr-group">{{ $group }}</th>
                                @endif
                            @endforeach
                            @foreach (collect($report['columns'])->groupBy('month', true) as $month => $cols)
                                <th colspan="{{ count($cols) }}" class="rr-month">{{ $month }}</th>
                            @endforeach
                        </tr>
                        <tr>
                            @foreach ($groups as $cols)
                                @foreach ($cols as [$key, $label, $type])
                                    <th class="{{ $key === 'reference' ? 'rr-sticky' : ($key === 'name' ? 'rr-sticky-2' : '') }} {{ in_array($type, ['int', 'dec', 'pct', 'money']) ? 'rr-num' : '' }}">
                                        {{ $label }}
                                        @if (! empty($cols[$loop->index][3]))
                                            <span class="rr-help" x-tooltip.raw="{{ $cols[$loop->index][3] }}" title="{{ $cols[$loop->index][3] }}">?</span>
                                        @endif
                                    </th>
                                @endforeach
                            @endforeach
                            {!! collect($report['columns'])->map(fn ($col, $i) => '<th class="pc' . ($monthStarts[$i] ? ' rr-month' : '') . '">' . e($col['label']) . '</th>')->implode('') !!}
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($report['rows'] as $row)
                            <tr wire:key="rr-{{ $row['id'] }}" data-ge="{{ $row['reference'] ?: $row['name'] }}">
                                @foreach ($groups as $cols)
                                    @foreach ($cols as [$key, $label, $type])
                                        <td class="{{ $key === 'reference' ? 'rr-sticky font-semibold' : ($key === 'name' ? 'rr-sticky-2' : '') }} {{ in_array($type, ['int', 'dec', 'pct', 'money']) ? 'rr-num' : '' }} {{ $key === 'orders' ? 'rr-orders' : '' }}"
                                            @if (in_array($key, ['name', 'orders', 'customer', 'rental_status'])) title="{{ $row[$key] }}" @endif>
                                            @if ($key === 'status' && $row['status_colors'])
                                                <span class="rr-badge" style="background: {{ $row['status_colors'][0] }}; color: {{ $row['status_colors'][1] }};">{{ $row['status'] }}</span>
                                            @else
                                                {{ $fmt($row[$key] ?? null, $type) }}
                                            @endif
                                        </td>
                                    @endforeach
                                @endforeach
                                {{-- Cases du planning générées en une chaîne : pas de marqueurs Livewire/Blade par case
                                     (13 000+ cases). data-c = index de colonne ; libellé construit en JS au survol / clic. --}}
                                @php
                                    $html = '';
                                    foreach ($row['cells'] as $i => $cell) {
                                        $month = $monthStarts[$i] ? ' rr-month' : '';
                                        if (! $cell) {
                                            $html .= '<td class="pc' . $month . '"></td>';
                                            continue;
                                        }
                                        $html .= '<td class="pc u-' . $cell['code'] . $month . '" data-c="' . $i . '"'
                                            . ($report['granularity'] === 'week' ? ' data-n="' . $cell['days'] . '"' : '')
                                            . ' data-r="' . e(implode('|', $cell['refs'])) . '">' . $cell['code'] . '</td>';
                                    }
                                @endphp
                                {!! $html !!}
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    @endif
</x-filament-panels::page>
