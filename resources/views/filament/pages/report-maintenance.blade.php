@php
    $total1 = $data != null ? $data->sum('devis_montant') + $data->sum('montant_piece') : 0;
    $total2 = $data != null ? $total1 + $data->first()->montant_paye : 0;
@endphp
<x-filament-panels::page>
    <script src="{{ asset('css/pub.css') }}"></script>
    <div id="printable" style="font-family: Helvetica, Arial, sans-serif;">
        <x-report-header />

        <x-daily-report-header title="Rapport des maintenances" :contracts="$contracts" city="Brazzaville" />

        <h3 class="text-lg font-semibold mb-2">Interventions</h3>
        <table class="min-w-full divide-y divide-slate-800">
            <thead style="display: table-row-group">
                <tr>
                    {{-- <th class="px-3 py-4 text-sm text-left font-bold text-slate-800 border border-slate-400">Date</th> --}}
                    <th class="px-3 py-4 text-sm text-left font-bold text-slate-800 border border-slate-400">Num
                    </th>
                    <th class="px-3 py-4 text-sm font-bold text-left text-slate-800 border border-slate-400">Type</th>
                    {{-- <th class="px-3 py-4 text-sm font-bold text-left text-slate-800 border border-slate-400">Techniciens
                    </th> --}}
                    <th class="px-3 py-4 text-sm text-left font-bold text-slate-800 border border-slate-400">Pièces
                    </th>
                    <th class="px-3 py-4 text-sm font-bold text-left text-slate-800 border border-slate-400">QT</th>
                    <th class="px-3 py-4 text-sm font-bold text-left text-slate-800 border border-slate-400">Prix Total</th>
                    <th class="px-3 py-4 text-sm font-bold text-left text-slate-800 border border-slate-400">Montant
                        Int.</th>
                    <th
                        class="px-3 py-4 text-sm font-bold text-left text-slate-800 border border-slate-400 text-nowrap">
                        MT(FCFA)
                    </th>
                </tr>
            </thead>
            <tbody>

                @if ($data !== null)
                    @foreach ($data as $intervention)
                        <tr class="border-b border-slate-400">
                            {{-- <td class="px-3 py-4 text-sm text-left text-slate-800 border border-slate-400">{{ \App\Utils\DateUtils::format($intervention->date) }}</td> --}}
                            <td class="px-3 py-4 text-sm text-left text-slate-800 border border-slate-400">
                                <a href="{{ url('/interventions/' . $intervention->id) }}" class="text-blue-600 hover:text-blue-800">
                                    {{ $intervention->identifiant }}
                                </a></td>
                            <td class="px-3 py-4 text-blue-600 text-sm text-left text-slate-800 border border-slate-400">
                                {{ \App\Enum\InterventionType::from($intervention->type_intervention)->label() }}</td>
                            {{-- <td class="px-3 py-4 text-[12px] text-left text-slate-800 border border-slate-400">
                                {{ $intervention->techniciens }}</td> --}}
                            <td class="px-3 py-4 text-[12px] text-sm text-left text-slate-800 border border-slate-400">
                                {{ $intervention->pieces }}</td>
                            <td class="px-3 py-4 text-sm text-left text-slate-800 border border-slate-400">
                                {{ $intervention->total_pieces }}</td>
                            <td class="px-3 py-4 text-sm text-left text-slate-800 border border-slate-400">
                                {{ \App\Utils\NumberUtils::format($intervention->montant_piece) }}</td>
                            <td class="px-3 py-4 text-sm text-left text-slate-800 border border-slate-400">
                                {{ \App\Utils\NumberUtils::format($intervention->devis_montant) }}</td>
                            <td class="px-3 py-4 text-sm text-left font-bold text-slate-800 border border-slate-400">
                                {{ \App\Utils\NumberUtils::format($intervention->devis_montant + $intervention->montant_piece) }}
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
            <tfoot style="display: table-row-group">
                <tr>
                    <th colspan="3" class="text-left px-3 py-4 text-sm text-slate-800">
                        Total
                    </th>

                    <th class="px-3 py-4 text-sm text-left font-bold text-slate-800 border border-slate-400">
                        {{ \App\Utils\NumberUtils::format($data != null ? $data->sum('total_pieces') : 0) }}
                    </th>
                    <th class="px-3 py-4 text-sm text-left font-bold text-slate-800 border border-slate-400">
                        {{ \App\Utils\NumberUtils::format(number: $data != null ? $data->sum('montant_piece'): 0)  }}
                    </th>
                    <th class="px-3 py-4 text-sm text-left font-bold text-slate-800 border border-slate-400">
                        {{ \App\Utils\NumberUtils::format($data != null ? $data->sum('devis_montant') :0)  }}
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
                    <th class="px-3 py-4 text-sm font-bold text-center text-slate-800 border border-slate-400">Durée
                    </th>
                    <th class="px-3 py-4 text-sm font-bold text-center text-slate-800 border border-slate-400">Ecoulés
                    </th>
                    <th class="px-3 py-4 text-sm font-bold text-center text-slate-800 border border-slate-400">
                        Interventions</th>
                    <th class="px-3 py-4 text-sm font-bold text-center text-slate-800 border border-slate-400">Forfait
                    </th>
                    <th class="px-3 py-4 text-sm font-bold text-center text-slate-800 border border-slate-400">Payé</th>
                    <th class="px-3 py-4 text-sm font-bold text-center text-slate-800 border border-slate-400">Revenu
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="px-3 py-2 font-semibold text-right text-slate-800 border border-slate-400">
                        {{ $data != null ? $data->first()->duree_contrat : "" }} mois
                    </td>
                    <td class="px-3 py-2 font-semibold text-right text-slate-800 border border-slate-400">
                        {{ $data != null ? $data->first()->mois_ecoules :"" }} mois
                    </td>
                    <td class="px-3 py-2 font-semibold text-right text-slate-800 border border-slate-400">
                        {{ $data != null ? $data->count() :"" }}
                    </td>
                    <td class="px-3 py-2 font-semibold text-right text-slate-800 border border-slate-400">
                        {{ $data != null ? \App\Utils\NumberUtils::format($data->first()->forfait) :"" }}
                    </td>
                    <td class="px-3 py-2 font-semibold text-right text-slate-800 border border-slate-400">
                        {{ $data != null ? \App\Utils\NumberUtils::format($data->first()->montant_paye) :"" }}
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
    <style>
        @media print {
            tfoot {
                display: table-footer-group;
            }
        }
    </style>
</x-filament-panels::page>
