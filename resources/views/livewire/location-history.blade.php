@php
    use App\Enum\GeneratorStatus;
    use App\Enum\RentalUsage;
    use App\Utils\DateUtils;
    use App\Utils\NumberUtils;

    $th = 'px-3 py-3 text-sm text-left font-bold text-slate-800 border border-slate-400';
    $td = 'px-3 py-3 text-sm text-left text-slate-800 border border-slate-400';
    $statusLabels = [
        'draft' => 'Devis', 'sent' => 'Envoyé', 'sale' => 'Confirmé', 'done' => 'Verrouillé', 'cancel' => 'Annulé',
    ];
@endphp

<div class="space-y-6">
<table class="min-w-full divide-y divide-slate-800">
    <thead style="display: table-row-group">
        <tr>
            <th class="{{ $th }}">Type</th>
            <th class="{{ $th }}">Numéro</th>
            <th class="{{ $th }}">Client</th>
            <th class="{{ $th }}">Date départ</th>
            <th class="{{ $th }}">Date retour</th>
            <th class="{{ $th }}">Montant HT (FCFA)</th>
            <th class="{{ $th }}">Factures</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($data as $period)
            @php($usage = $usageCustomers[$period->customer_id] ?? RentalUsage::LOCATION)
            <tr class="border-b border-slate-400">
                <td class="{{ $td }}">
                    <span style="background: {{ $usage->colors()[0] }}; color: {{ $usage->colors()[1] }}; padding: 1px 6px; border-radius: 4px; font-weight: 700; font-size: 11px;">{{ $usage->value }}</span>
                    {{ $usage === RentalUsage::LOCATION ? 'Loué' : $usage->label() }}
                </td>
                <td class="{{ $td }} font-bold">
                    @if ($period->devis_id)
                        <a target="_blank" href="{{ url('/admin/devis/' . $period->devis_id) }}" class="text-blue-600">{{ $period->order_number }}</a>
                    @else
                        {{ $period->order_number }}
                    @endif
                    <div class="text-xs font-normal text-slate-500">{{ $statusLabels[$period->order_state] ?? $period->order_state }}</div>
                </td>
                <td class="{{ $td }} text-blue-600">
                    {{ $period->customer?->name }}
                    @if ($period->customer?->contact_c_phone)<div class="text-[12px] text-slate-500">{{ $period->customer->contact_c_phone }}</div>@endif
                </td>
                <td class="{{ $td }}">
                    @if ($period->picked_up_at)
                        {{ DateUtils::format($period->picked_up_at) }}
                    @elseif ($period->planned_start)
                        <span class="italic text-slate-500" title="Date prévue (pas de mouvement de départ dans Odoo)">{{ DateUtils::format($period->planned_start) }} (prévu)</span>
                    @endif
                </td>
                <td class="{{ $td }}">
                    @if ($period->returned_at)
                        {{ DateUtils::format($period->returned_at) }}
                    @elseif ($period->isOngoing())
                        <span class="font-semibold" style="color: #d97706;">En cours</span>
                        @if ($period->planned_return)<div class="text-xs italic text-slate-500">prévu {{ DateUtils::format($period->planned_return) }}</div>@endif
                    @elseif ($period->planned_return)
                        <span class="italic text-slate-500" title="Date prévue (pas de mouvement de retour dans Odoo)">{{ DateUtils::format($period->planned_return) }} (prévu)</span>
                    @endif
                </td>
                <td class="{{ $td }} font-bold text-right">{{ NumberUtils::format($period->amount_untaxed) }}</td>
                <td class="{{ $td }} text-[12px]">{{ $period->invoice_numbers ?? ($period->invoice_status === 'invoiced' ? 'Facturé' : '—') }}</td>
            </tr>
        @empty
            <tr><td colspan="7" class="{{ $td }} text-center text-slate-500">Aucune location importée d'Odoo pour ce groupe.</td></tr>
        @endforelse
    </tbody>
</table>

@if ($loans->isNotEmpty())
    <div>
        <h4 class="font-bold text-sm mb-2">
            <span style="background: {{ RentalUsage::PRET->colors()[0] }}; color: #fff; padding: 1px 6px; border-radius: 4px; font-size: 11px;">P</span>
            Remplacements (prêts)
        </h4>
        <table class="min-w-full divide-y divide-slate-800">
            <thead style="display: table-row-group">
                <tr>
                    <th class="{{ $th }}">Rôle</th>
                    <th class="{{ $th }}">Devis</th>
                    <th class="{{ $th }}">Intervention</th>
                    <th class="{{ $th }}">Autre GE</th>
                    <th class="{{ $th }}">Du</th>
                    <th class="{{ $th }}">Au</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($loans as $loan)
                    <tr>
                        <td class="{{ $td }} font-bold">{{ $loan['role'] !== 'loaned' ? 'Remplacé (en révision)' : ($loan['replaced_id'] === null ? 'Prêté (statut)' : 'Prêté (remplace)') }}</td>
                        <td class="{{ $td }}">{{ $loan['order_number'] ?? '—' }}</td>
                        <td class="{{ $td }}">{{ $loan['intervention'] }}</td>
                        <td class="{{ $td }}">{{ $loan['other'] ?? '—' }}</td>
                        <td class="{{ $td }}">{{ $loan['start']->format('d/m/Y') }}</td>
                        <td class="{{ $td }}">
                            @if ($loan['ongoing'])
                                <span class="font-semibold" style="color: #d97706;">En cours</span>
                            @else
                                {{ $loan['end']->format('d/m/Y') }}
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

@if ($consumptions->isNotEmpty())
    <div>
        <h4 class="font-bold text-sm mb-2">
            <span style="background: {{ RentalUsage::MAINTENANCE->colors()[0] }}; color: #fff; padding: 1px 6px; border-radius: 4px; font-size: 11px;">M</span>
            Consommations internes (maintenance) — {{ NumberUtils::format($consumptions->whereIn('order_state', ['sale', 'done'])->sum('amount_untaxed')) }} FCFA HT
        </h4>
        <table class="min-w-full divide-y divide-slate-800">
            <thead style="display: table-row-group">
                <tr>
                    <th class="{{ $th }}">Date</th>
                    <th class="{{ $th }}">Numéro</th>
                    <th class="{{ $th }}">Intervention</th>
                    <th class="{{ $th }}">Compteur</th>
                    <th class="{{ $th }}">Montant HT (FCFA)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($consumptions as $c)
                    <tr>
                        <td class="{{ $td }}">{{ $c->order_date?->format('d/m/Y') }}</td>
                        <td class="{{ $td }} font-bold">
                            {{ $c->order_number }}
                            <div class="text-xs font-normal text-slate-500">{{ $statusLabels[$c->order_state] ?? $c->order_state }}</div>
                        </td>
                        <td class="{{ $td }}">{{ $c->description ?? '—' }}</td>
                        <td class="{{ $td }}">{{ $c->hours ? NumberUtils::format($c->hours) . ' h' : '—' }}</td>
                        <td class="{{ $td }} font-bold text-right">{{ NumberUtils::format($c->amount_untaxed) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

@if ($statusLogs->isNotEmpty())
    <div>
        <h4 class="font-bold text-sm mb-2">Changements de statut</h4>
        <table class="min-w-full divide-y divide-slate-800">
            <thead style="display: table-row-group">
                <tr>
                    <th class="{{ $th }}">Date</th>
                    <th class="{{ $th }}">Ancien statut</th>
                    <th class="{{ $th }}">Nouveau statut</th>
                    <th class="{{ $th }}">Par</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($statusLogs as $log)
                    <tr>
                        <td class="{{ $td }}">{{ $log->created_at?->format('d/m/Y H:i') }}</td>
                        <td class="{{ $td }}">{{ GeneratorStatus::tryFrom((string) $log->old_status)?->label() ?? '—' }}</td>
                        <td class="{{ $td }} font-bold">{{ GeneratorStatus::tryFrom((string) $log->new_status)?->label() ?? '—' }}</td>
                        <td class="{{ $td }}">{{ $log->user?->name ?? 'Système' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
</div>
