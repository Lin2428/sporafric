@php
    if($data == null){
        $data = collect();
    }
    $workDays = 0;
    $txOcupation = 0;
    $total1 = 0;
    $total2 = 0;
    $revenuContrat = 0;
    $forfait = 0;

    if($data->isNotEmpty()){
        $total1 = $data->sum('montant') + $data->sum('montant_piece');
        

        $workDays = $data->first()->occupation;

        $workDays +=  collect($data)
            ->unique('contract_id') 
            ->where('occupation', 0)
            ->sum(fn($report) => (int) -(now()->diffInDay($report->contract_start_at)));

        $txOcupation = ($workDays / 360) * 100;

        $revenuContrat = $data
        ->unique('generator_id')
            ->sum('montant_paye');

        $forfait = $data
        ->unique('generator_id')
        ->sum('forfait');

            $total2 = $total1 + $revenuContrat;

    }

@endphp
<x-filament-panels::page>
    <script src="{{ asset('css/pub.css') }}"></script>
    <div id="printable" style="font-family: Helvetica, Arial, sans-serif;">
        <x-report-header />

        <x-daily-report-header title="Rapport de maintenance" :contracts="$contracts" />

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

                @if ($data->isNotEmpty())
                    @foreach ($data as $intervention)
                        <tr class="border-b border-slate-400">
                            {{-- <td class="px-3 py-4 text-sm text-left text-slate-800 border border-slate-400">{{ \App\Utils\DateUtils::format($intervention->date) }}</td> --}}
                            <td class="px-3 py-4 text-sm text-left text-slate-800 border border-slate-400">
                                <a target="_blank" href="{{ url('/admin/interventions/' . $intervention->id) }}" class="text-blue-600 ">
                                    #{{ $intervention->identifiant }}
                                </a></td>
                            <td class="px-3 py-4 text-blue-600 text-sm text-left border border-slate-400">
                                {{ \App\Enum\InterventionType::from($intervention->type_intervention)->label() }}</td>
                            {{-- <td class="px-3 py-4 text-[12px] text-left text-slate-800 border border-slate-400">
                                {{ $intervention->techniciens }}</td> --}}
                            <td class="px-3 py-4 text-[12px] text-sm text-left text-slate-800 border border-slate-400">
                                {{ $intervention->pieces }}</td>
                            <td class="px-3 py-4 text-sm text-left text-slate-800 border border-slate-400">
                                {{ $intervention->total_pieces }}</td>
                            <td class="px-3 py-4 text-sm text-right text-slate-800 border border-slate-400">
                                {{ \App\Utils\NumberUtils::format($intervention->montant_piece) }}</td>
                            <td class="px-3 py-4 text-sm text-right text-slate-800 border border-slate-400">
                                {{ \App\Utils\NumberUtils::format($intervention->montant) }}</td>
                            <td class="px-3 py-4 text-sm text-right font-bold text-slate-800 border border-slate-400">
                                {{ \App\Utils\NumberUtils::format($intervention->montant + $intervention->montant_piece) }}
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

                    <th class="px-3 py-4 text-sm text-right font-bold text-slate-800 border border-slate-400">
                        {{ \App\Utils\NumberUtils::format($data->isNotEmpty() ? $data->sum('total_pieces') : 0) }}
                    </th>
                    <th class="px-3 py-4 text-sm text-right font-bold text-slate-800 border border-slate-400">
                        {{ \App\Utils\NumberUtils::format(number: $data->isNotEmpty() ? $data->sum('montant_piece'): 0)  }}
                    </th>
                    <th class="px-3 py-4 text-sm text-right font-bold text-slate-800 border border-slate-400">
                        {{ \App\Utils\NumberUtils::format($data->isNotEmpty() ? $data->sum('montant') :0)  }}
                    </th>
                    <th class="px-3 py-4 text-sm text-right font-bold text-slate-800 border border-slate-400">
                        {{ \App\Utils\NumberUtils::format($total1) }}
                    </th>
                </tr>
            </tfoot>
        </table>

        @if($type === '0')
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
                     <th class="px-3 py-4 text-sm font-bold text-center text-slate-800 border border-slate-400">
                        Nombre GE</th>
                    <th class="px-3 py-4 text-sm font-bold text-center text-slate-800 border border-slate-400">Forfait
                    </th>
                    <th class="px-3 py-4 text-sm font-bold text-center text-slate-800 border border-slate-400">Payé</th>
                    <th class="px-3 py-4 text-sm font-bold text-center text-slate-800 border border-slate-400">Revenu
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="px-3 py-2 text-sm font-semibold text-right text-slate-800 border border-slate-400">
                        {{ $data->isNotEmpty() ? $data->first()->duree_contrat : "" }} mois
                    </td>
                    <td class="px-3 py-2 text-sm font-semibold text-right text-slate-800 border border-slate-400">
                        {{ $data->isNotEmpty() ? $data->first()->mois_ecoules :"" }} mois
                    </td>
                    <td class="px-3 py-2 text-sm font-semibold text-right text-slate-800 border border-slate-400">
                        {{ $data->isNotEmpty() ? $data->count() :"" }}
                    </td>
                    <td class="px-3 py-2 text-sm font-semibold text-right text-slate-800 border border-slate-400">
                        @php
                         if($data->isNotEmpty()){
                         $count = $data->unique('generator_id')->count();
                         $countNewGenerator = $data->whereNotNull('new_generator_id')->unique('new_generator_id')->count() ?? 0;
                         $count -= $countNewGenerator;
                         }
                        @endphp
                        {{ $data->isNotEmpty() ? $count :"" }}
                    </td>
                    <td class="px-3 py-2 text-sm font-semibold text-right text-slate-800 border border-slate-400">
                        {{ $data->isNotEmpty() ? \App\Utils\NumberUtils::format($forfait) :"" }}
                    </td>
                    <td class="px-3 py-2 text-sm font-semibold text-right text-slate-800 border border-slate-400">
                        {{ $data->isNotEmpty() ? \App\Utils\NumberUtils::format($revenuContrat) :"" }}
                    </td>
                    <td class="px-3 py-2 text-sm font-semibold text-right text-slate-800 border border-slate-400">
                        {{ \App\Utils\NumberUtils::format($total2) }} FCFA
                    </td>
                </tr>
            </tbody>
        </table>
        @endif


        @if($type === '1')
        <h3 class="text-lg font-semibold pt-5 mb-3">Récaputilatif du Groupe Electrogène</h3>
        <table class="min-w-full border border-slate-400 divide-y divide-slate-300">
            <thead>
                <tr>
                    <th class="px-3 py-4 text-sm font-bold text-center text-slate-800 border border-slate-400">Activité
                    </th>
                    <th class="px-3 py-4 text-sm font-bold text-center text-slate-800 border border-slate-400">Tx Occupation
                    </th>
                    <th class="px-3 py-4 text-sm font-bold text-center text-slate-800 border border-slate-400">Interventions
                        
                    </th>
                    <th class="px-3 py-4 text-sm font-bold text-center text-slate-800 border border-slate-400">Revenu Contrats
                    </th>
                    <th class="px-3 py-4 text-sm font-bold text-center text-slate-800 border border-slate-400">Total
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="px-3 py-2 text-sm font-semibold text-right text-slate-800 border border-slate-400">
                        {{ \App\Utils\NumberUtils::format((int)$workDays) }} jours
                    </td>
                    <td class="px-3 py-2 text-sm font-semibold text-right text-slate-800 border border-slate-400">
                        {{ \App\Utils\NumberUtils::format($txOcupation) }}%
                    </td>
                    <td class="px-3 py-2 text-sm font-semibold text-right text-slate-800 border border-slate-400">
                        {{ $data->isNotEmpty() ? $data->count() :"" }}
                    </td>
                    <td class="px-3 py-2 text-sm font-semibold text-right text-slate-800 border border-slate-400">
                        {{ \App\Utils\NumberUtils::format($revenuContrat)}}
                    </td>
                    <td class="px-3 py-2 text-sm font-semibold text-right text-slate-800 border border-slate-400">
                        {{ \App\Utils\NumberUtils::format($total1 + $revenuContrat) }} FCFA
                    </td>
                </tr>
            </tbody>
        </table>
        @endif
    </div>

    

    <x-filament::button id="print-form-etat">Imprimer</x-filament::button>

    <script src="{{ asset('js/pub.js') }}"></script>
    <style>
        @media print {
            tfoot {
                display: table-footer-group;
            }

             .fi-header {
                display: none;
            }

            @page {
                margin: 20px 40px 10px 40px; /* top, right, bottom, left */
            }

            body {
                margin: 0; /* Réinitialise les marges internes */
            }
        }
    </style>
</x-filament-panels::page>
