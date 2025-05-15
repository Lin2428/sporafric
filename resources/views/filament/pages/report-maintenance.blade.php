@php
$total1 = $data->sum('devis_montant') + $data->sum('montant_piece');
$total2 = $total1 + $data->first()->montant_paye;
@endphp
<x-filament-panels::page >
    <div id="printable" style="font-family: Helvetica, Arial, sans-serif;">
        <x-report-header/>

        <x-daily-report-header
            title="Rapport des maintenance"
            :contracts="$contracts"
            city="Brazzaville" />

        <h3 class="text-lg font-semibold mb-2">Interventions</h3>
        <table class="min-w-full divide-y divide-slate-800">
            <thead>
            <tr>
                {{-- <th class="px-3 py-4 text-sm text-left font-bold text-slate-800 border border-slate-400">Date</th> --}}
                <th class="px-3 py-4 text-sm text-left font-bold text-slate-800 border border-slate-400">Intervention</th>
                <th class="px-3 py-4 text-sm font-bold text-left text-slate-800 border border-slate-400">Type</th>
                <th class="px-3 py-4 text-sm font-bold text-left text-slate-800 border border-slate-400">Techniciens</th>
                <th class="px-3 py-4 text-sm text-left font-bold text-slate-800 border border-slate-400">Pièces
                </th>
                <th class="px-3 py-4 text-sm font-bold text-left text-slate-800 border border-slate-400">QT</th>
                <th class="px-3 py-4 text-sm font-bold text-left text-slate-800 border border-slate-400">PT</th>
                <th class="px-3 py-4 text-sm font-bold text-left text-slate-800 border border-slate-400">Montant Int.</th>
                <th class="px-3 py-4 text-sm font-bold text-left text-slate-800 border border-slate-400 text-nowrap">
                    MT(FCFA)
                </th>
            </tr>
            </thead>
            <tbody>
            @foreach($data as $intervention)
                <tr class="border-b border-slate-400">
                    {{-- <td class="px-3 py-4 text-sm text-left text-slate-800 border border-slate-400">{{ \App\Utils\DateUtils::format($intervention->date) }}</td> --}}
                    <td class="px-3 py-4 text-sm text-left text-slate-800 border border-slate-400">{{ $intervention->identifiant }}</td>
                    <td class="px-3 py-4 text-sm text-left text-slate-800 border border-slate-400">{{ \App\Enum\InterventionType::from($intervention->type_intervention)->label() }}</td>
                    <td class="px-3 py-4 text-sm text-left text-slate-800 border border-slate-400">{{ $intervention->techniciens }}</td>
                    <td class="px-3 py-4 text-sm text-left text-slate-800 border border-slate-400">{{ $intervention->pieces }}</td>
                    <td class="px-3 py-4 text-sm text-left text-slate-800 border border-slate-400">{{ $intervention->total_pieces }}</td>
                    <td class="px-3 py-4 text-sm text-left text-slate-800 border border-slate-400">{{ \App\Utils\NumberUtils::format( $intervention->montant_piece) }}</td>
                    <td class="px-3 py-4 text-sm text-left text-slate-800 border border-slate-400">{{ \App\Utils\NumberUtils::format($intervention->devis_montant) }}</td>
                    <td class="px-3 py-4 text-sm text-left font-bold text-slate-800 border border-slate-400">{{\App\Utils\NumberUtils::format($intervention->devis_montant + $intervention->montant_piece)}}</td>
                </tr>
            @endforeach                      
            </tbody>
            <tfoot>
            <tr>
                <th colspan="4" class="text-left px-3 py-4 text-sm text-slate-800">
                    Total
                </th>
                
                <th class="px-3 py-4 text-sm text-left font-bold text-slate-800 border border-slate-400">
                    {{ \App\Utils\NumberUtils::format($data->sum('total_pieces')) }}
                </th>
                <th class="px-3 py-4 text-sm text-left font-bold text-slate-800 border border-slate-400">
                    {{ \App\Utils\NumberUtils::format($data->sum('montant_piece')) }}
                </th>
                <th class="px-3 py-4 text-sm text-left font-bold text-slate-800 border border-slate-400">
                    {{ \App\Utils\NumberUtils::format($data->sum('devis_montant')) }}
                </th>
                <th class="px-3 py-4 text-sm text-left font-bold text-slate-800 border border-slate-400">
                    {{ \App\Utils\NumberUtils::format($total1) }}
                </th>
            </tr>
            </tfoot>
        </table>

        <h3 class="text-lg font-semibold pt-5 mb-3">Récaputilatif du contrat</h3>
        <table class="min-w-full border border-slate-400 divide-y divide-slate-300">
            <thead>
            <tr>
                <th class="px-3 py-4 text-sm font-bold text-center text-slate-800 border border-slate-400">Durée</th>
                <th class="px-3 py-4 text-sm font-bold text-center text-slate-800 border border-slate-400">Ecoulés</th>
                <th class="px-3 py-4 text-sm font-bold text-center text-slate-800 border border-slate-400">Interventions</th>
                <th class="px-3 py-4 text-sm font-bold text-center text-slate-800 border border-slate-400">Forfait</th>
                <th class="px-3 py-4 text-sm font-bold text-center text-slate-800 border border-slate-400">Payé</th>
                <th class="px-3 py-4 text-sm font-bold text-center text-slate-800 border border-slate-400">Total</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="px-3 py-2 font-semibold text-right text-slate-800 border border-slate-400">
                    {{ $data->first()->duree_contrat }} mois
                </td>
                <td class="px-3 py-2 font-semibold text-right text-slate-800 border border-slate-400">
                    {{ $data->first()->mois_ecoules }} mois
                </td>
                <td class="px-3 py-2 font-semibold text-right text-slate-800 border border-slate-400">
                    {{ $data->count() }}
                </td>
                <td class="px-3 py-2 font-semibold text-right text-slate-800 border border-slate-400">
                    {{ \App\Utils\NumberUtils::format($data->first()->forfait) }}
                </td>
                <td class="px-3 py-2 font-semibold text-right text-slate-800 border border-slate-400">
                    {{ \App\Utils\NumberUtils::format($data->first()->montant_paye) }}
                </td>
                <td class="px-3 py-2 font-semibold text-right text-slate-800 border border-slate-400">
                    {{ \App\Utils\NumberUtils::format($total2) }} FCFA
                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <x-filament::button id="print-form-etat">Imprimer</x-filament::button>

    <script src="{{ asset('js/pub.js') }}"></script>
</x-filament-panels::page>

