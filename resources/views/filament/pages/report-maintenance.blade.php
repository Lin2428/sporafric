@php
if ($data == null) {
$data = collect();
}
$workDays = 0;
$txOcupation = 0;
$total1 = 0;
$total2 = 0;
$revenuContrat = 0;
$forfait = 0;

//variable d'impression de colone
$printDebut = in_array('start_at', $printableFields);
$printFin = in_array('end_at', $printableFields);
$printSite = in_array('site', $printableFields);
$printTech = in_array('technicien', $printableFields);
$printGen = in_array('generator', $printableFields) && $this->generatorId == null;
$printGenHoure = in_array('generator', $printableFields);
$printPiece = in_array('piece', $printableFields);
$printQtyPiece = in_array('qty_piece', $printableFields);
$printMPieces = in_array('amount_pieces', $printableFields);
$printMInt = in_array('amount_intervention', $printableFields);
$printMTotal = in_array('amount_total', $printableFields);

// Colonnes de base (N°, N° BT, Devis, Type, Date planifiée) = 5
$baseColumns = 5;

// Compter les colonnes optionnelles affichées AVANT les totaux
$optionalBeforeTotals = 0;
if ($printDebut) {
$optionalBeforeTotals++;
}
if ($printFin) {
$optionalBeforeTotals++;
}
if ($printSite) {
$optionalBeforeTotals++;
}
if ($printTech) {
$optionalBeforeTotals++;
}
if ($printGen) {
$optionalBeforeTotals++;
}
if ($printGenHoure) {
$optionalBeforeTotals++;
}
if ($printPiece) {
$optionalBeforeTotals++;
}

$rowTotal = $baseColumns + $optionalBeforeTotals;
if ($data->isNotEmpty()) {
if ($data->isNotEmpty()) {
$total1 = $data->sum('montant') + $data->sum('montant_piece');

$workDays = $data->first()->occupation;

$workDays += collect($data)
->unique('contract_id')
->where('occupation', 0)
->sum(fn($report) => (int) -now()->diffInDay($report->contract_start_at));

$txOcupation = ($workDays / 360) * 100;

$revenuContrat = $data->unique('generator_id')->sum('montant_paye');

$forfait = $data->unique('generator_id')->sum('forfait');

$total2 = $total1 + $revenuContrat;
}
}

@endphp
<x-filament-panels::page>
    <script src="{{ asset('css/pub.css') }}"></script>
    <div id="printable" style="font-family: Helvetica, Arial, sans-serif;">
        <x-report-header />

        <x-daily-report-header title="Rapport de maintenance" :contracts="$contracts" />
        <br>
        <div class="info-container text-[13px] flex justify-between  text-gray-700">

            <div>
                @if ($this->customer != null)
                <span> Client : {{ $this->customer->name }}</span> <br>
                <span>Telephone : {{ $this->customer->contact_c_phone }}</span>
                @endif
                @if ($this->contract != null)

                <div claqq="text-[12px]">
                    @if ($this->customer == null)
                    <span>Client : {{ $this->contract->customer->name }}</span>
                    <br>
                    <span>Telephone : {{ $this->contract->customer->contact_c_phone }}</span>
                    <br>
                    @endif
                    <span>Contrat : {{ $this->contract->number }}</span>
                </div>

                @endif
                @if ($this->generator != null)
                <div>
                    <span>Groupe Eléctrogène : {{ $this->generator->name }}</span>
                    <br>
                    <span>Puissance : {{ $this->generator->power }} kVA</span>
                </div>
                @endif
                @if ($this->interventionType != null)
                <div>
                    <span>Type d'intervention :
                        {{ \App\Enum\InterventionType::from($this->interventionType)->label() }}
                    </span>
                </div>
                @endif
            </div>
            @if ($this->selectDateRange != null)
            <div class="flex items-end">
                <p>
                    Période : du {{ $this->selectDateRange }}
                </p>
            </div>
            @endif
        </div>
        <br>
        <h3 class="text-lg font-semibold mb-2 mt-5">Interventions</h3>
        <div class="overflow-x-auto div-table">
            <table class="w-max divide-y divide-slate-800 ">
                <thead style="display: table-row-group">
                    <tr>
                        {{-- <th
                            class="custom-padding text-[12px] text-left font-bold text-slate-800 border border-slate-400">
                            Date</th> --}}
                        <th
                            class="custom-padding text-[12px] text-left font-bold text-slate-800 border border-slate-400 max-w-[100px]">
                            N°
                        </th>
                        <th
                            class="custom-padding text-[12px] text-left font-bold text-slate-800 border border-slate-400 max-w-[60px]">
                            N° BT
                        </th>
                        <th
                            class="custom-padding text-[12px] text-left font-bold text-slate-800 border border-slate-400 whitespace-nowrap">
                            Devis
                        </th>
                        <th
                            class="custom-padding text-[12px] font-bold text-left text-slate-800 border border-slate-400 max-w-[100px]">
                            Type
                        </th>
                        <th
                            class="custom-padding text-[12px] font-bold text-left text-slate-800 border border-slate-400 whitespace-nowrap">
                            Date planifiée
                        </th>
                        @if ($printDebut)
                        <th
                            class="end_at custom-padding text-[12px] font-bold text-left text-red-600 border border-slate-400 max-w-[50px]">
                            Début
                        </th>
                        @endif
                        @if ($printFin)
                        <th
                            class="end_at custom-padding text-[12px] font-bold text-left text-red-600 border border-slate-400 max-w-[50px]">
                            Fin
                        </th>
                        @endif
                        @if ($printSite)
                        <th
                            class="site custom-padding text-[12px] font-bold text-left text-red-600 border border-slate-400 max-w-[150px]">
                            Site
                        </th>
                        @endif
                        @if ($printTech)
                        <th
                            class="technicien custom-padding text-[12px] text-left font-bold text-red-600 border border-slate-400 max-w-[150px]">
                            Techniciens
                        </th>
                        @endif
                        @if ($printGen)
                        <th
                            class="generator custom-padding text-[12px] text-left font-bold text-red-600 border border-slate-400 whitespace-nowrap">
                            GE
                        </th>
                        @endif
                        @if ($printGenHoure)
                        <th
                            class="custom-padding text-[12px] text-left font-bold text-red-600 border border-slate-400 whitespace-nowrap">
                            Nb hr
                        </th>
                        @endif
                        @if ($printPiece)
                        <th
                            class="piece custom-padding text-[12px] text-left font-bold text-red-600 border border-slate-400 max-w-[400px]">
                            Pièces
                        </th>
                        @endif
                        @if ($printQtyPiece)
                        <th
                            class="qty_piece custom-padding text-[12px] font-bold text-left text-red-600 border border-slate-400 whitespace-nowrap">
                            QT Pièces
                        </th>
                        @endif
                        @if ($printMPieces)
                        <th
                            class="amount_pieces custom-padding text-[12px] font-bold text-left text-red-600 border border-slate-400 whitespace-nowrap">
                            Prix
                            Total
                        </th>
                        @endif
                        @if ($printMInt)
                        <th
                            class="amount_intervention custom-padding text-[12px] font-bold text-left text-red-600 border border-slate-400 whitespace-nowrap">
                            M.
                            Int
                        </th>
                        @endif
                        @if ($printMTotal)
                        <th
                            class="total_mount custom-padding text-[12px] font-bold text-left text-red-600 border border-slate-400 whitespace-nowrap">
                            M. Total
                        </th>
                        @endif
                    </tr>
                </thead>
                <tbody>

                    @if ($data->isNotEmpty())
                    @foreach ($data as $intervention)
                    <tr class="border-b border-slate-400">
                        {{-- <td class="custom-padding text-[12px] text-left text-slate-800 border border-slate-400">{{
                                    \App\Utils\DateUtils::format($intervention->date) }}</td> --}}
                        <td
                            class="custom-padding text-[12px] text-left text-slate-800 border border-slate-400 max-w-[100px]">
                            <a target="_blank" href="{{ url('/admin/interventions/' . $intervention->id) }}"
                                class="text-blue-600 ">
                                #{{ $intervention->numero }}
                            </a>
                        </td>
                        <td
                            class="custom-padding text-[12px] text-left text-slate-800 border border-slate-400 max-w-[90px]">
                            {{ $intervention->identifiant }}
                        </td>
                        <td
                            class="custom-padding text-[12px] text-left text-slate-800 border border-slate-400 whitespace-nowrap">
                            {{ $intervention->numero_devis }}
                        </td>
                        <td
                            class="custom-padding text-blue-600 text-[12px] text-left border border-slate-400 max-w-[100px]">
                            {{ \App\Enum\InterventionType::from($intervention->type_intervention)->label() }}
                        </td>
                        <td class="custom-padding text-[12px] text-left border border-slate-400 whitespace-nowrap">
                            {{ $intervention?->intervention_at ? \App\Utils\DateUtils::formatWithTime($intervention->intervention_at) : '' }}
                        </td>
                        @if ($printDebut)
                        <td
                            class="custom-padding text-[12px] text-left text-slate-800 border border-slate-400 max-w-[50px]">
                            {{ $intervention?->intervention_start_at ? \App\Utils\DateUtils::formatForReport($intervention->intervention_start_at) : '' }}
                        </td>
                        @endif
                        @if ($printFin)
                        <td
                            class="custom-padding text-[12px] text-left text-slate-800 border border-slate-400 max-w-[50px]">
                            {{ $intervention?->intervention_end_at ? \App\Utils\DateUtils::formatForReport($intervention->intervention_end_at) : '' }}
                        </td>
                        @endif
                        @if ($printSite)
                        <td
                            class="custom-padding text-[12px] text-left text-slate-800 border border-slate-400 max-w-[120px]">
                            {{ $intervention->site }}
                        </td>
                        @endif
                        @if ($printTech)
                        <td
                            class="custom-padding text-[12px] text-left text-slate-800 border border-slate-400 max-w-[150px]">
                            {{ $intervention->techniciens }}
                        </td>
                        @endif
                        @if ($printGen)
                        <td
                            class="custom-padding text-[12px] text-left text-slate-800 border border-slate-400 whitespace-nowrap">
                            {{ $intervention->generator_name }}
                        </td>
                        @endif
                        @if ($printGenHoure)
                        <td
                            class="custom-padding text-[12px] text-left text-slate-800 border border-slate-400 whitespace-nowrap">
                            {{ $intervention->generator_houres }}
                        </td>
                        @endif
                        @if ($printPiece)
                        <td
                            class="custom-padding text-[12px] text-left text-slate-800 border border-slate-400 max-w-[400px]">
                            {{ $intervention->pieces }}
                        </td>
                        @endif
                        @if ($printQtyPiece)
                        <td
                            class="custom-padding text-[12px] text-left text-slate-800 border border-slate-400 whitespace-nowrap">
                            {{ $intervention->total_pieces }}
                        </td>
                        @endif
                        @if ($printMPieces)
                        <td
                            class="custom-padding text-[12px] text-right text-slate-800 border border-slate-400 whitespace-nowrap">
                            {{ \App\Utils\NumberUtils::format($intervention->montant_piece) }}
                        </td>
                        @endif
                        @if ($printMInt)
                        <td
                            class="custom-padding text-[12px] text-right text-slate-800 border border-slate-400 whitespace-nowrap">
                            {{ \App\Utils\NumberUtils::format($intervention->montant) }}
                        </td>
                        @endif
                        @if ($printMTotal)
                        <td
                            class="custom-padding text-[12px] text-right font-bold text-slate-800 border border-slate-400 whitespace-nowrap">
                            {{ \App\Utils\NumberUtils::format($intervention->montant + $intervention->montant_piece) }}
                        </td>
                        @endif
                    </tr>
                    @endforeach
                    @endif
                </tbody>
                @if ($printMTotal || $printMInt || $printMPieces || $printQtyPiece)
                <tbody class="print-footer">
                    <tr class="border border-slate-400">
                        <th colspan="{{ $rowTotal }}"
                            class="text-left custom-padding text-[12px] text-slate-800 border border-slate-400">
                            Total
                        </th>

                        @if ($printQtyPiece)
                        <th
                            class="custom-padding text-[12px] text-right font-bold text-slate-800 border border-slate-400">
                            {{ \App\Utils\NumberUtils::format($data->sum('total_pieces')) }}
                        </th>
                        @endif

                        @if ($printMPieces)
                        <th
                            class="custom-padding text-[12px] text-right font-bold text-slate-800 border border-slate-400">
                            {{ \App\Utils\NumberUtils::format($data->sum('montant_piece')) }}
                        </th>
                        @endif

                        @if ($printMInt)
                        <th
                            class="custom-padding text-[12px] text-right font-bold text-slate-800 border border-slate-400">
                            {{ \App\Utils\NumberUtils::format($data->sum('montant')) }}
                        </th>
                        @endif

                        @if ($printMTotal)
                        <th
                            class="custom-padding text-[12px] text-right font-bold text-slate-800 border border-slate-400">
                            {{ \App\Utils\NumberUtils::format($total1) }}
                        </th>
                        @endif
                    </tr>
                    </tfoot>
                    @endif
            </table>
        </div>

        {{-- Récapitulatif prioritaire : client, puis contrat, puis générateur --}}
        @if ($customer != null && $contract == null && $generator == null)
        <h3 class="text-lg font-semibold pt-5 mb-3">Récapitulatif du client</h3>
        <table class="min-w-full border border-slate-400 divide-y divide-slate-300">
            <thead>
                <tr>
                    <th class="custom-padding text-[12px] font-bold text-center text-slate-800 border border-slate-400">
                        Contrat</th>
                    <th class="custom-padding text-[12px] font-bold text-center text-slate-800 border border-slate-400">
                        Nombre GE</th>
                    <th class="custom-padding text-[12px] font-bold text-center text-slate-800 border border-slate-400">
                        Forfait/mois</th>
                </tr>
            </thead>
            <tbody>
                @php
                $contrats = $dataRecap->isNotEmpty() ? $dataRecap->unique('contract_id') : collect();
                @endphp
                @foreach ($contrats as $contrat)
                @php
                $contratData = $dataRecap->where('contract_id', $contrat->contract_id);
                $nbGE = $contratData->unique('generator_id')->count();
                $forfaitMois = $contratData->sum('forfait') ?? 0;
                @endphp
                <tr>
                    <td class="custom-padding text-[12px] text-left text-slate-800 border border-slate-400">
                        {{ $contrat->contract->number ?? '-' }}
                    </td>
                    <td class="custom-padding text-[12px] text-right text-slate-800 border border-slate-400">{{ $nbGE }}
                    </td>
                    <td class="custom-padding text-[12px] text-right text-slate-800 border border-slate-400">
                        {{ \App\Utils\NumberUtils::format($forfaitMois) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @elseif ($contract != null && $generator == null)
        @php

        @endphp
        <h3 class="text-lg font-semibold pt-5 mb-3">Récapitulatif du contrat</h3>
        <table class="min-w-full border border-slate-400 divide-y divide-slate-300">
            <thead>
                <tr>
                    <th class="custom-padding text-[12px] font-bold text-center text-slate-800 border border-slate-400">
                        Durée</th>
                    <!-- <th class="custom-padding text-[12px] font-bold text-center text-slate-800 border border-slate-400">
                        Ecoulés</th> -->
                    <th class="custom-padding text-[12px] font-bold text-center text-slate-800 border border-slate-400">
                        Interventions</th>
                    <th class="custom-padding text-[12px] font-bold text-center text-slate-800 border border-slate-400">
                        Nombre GE</th>
                    <th class="custom-padding text-[12px] font-bold text-center text-slate-800 border border-slate-400">
                        Forfait / mois</th>
                    <th class="custom-padding text-[12px] font-bold text-center text-slate-800 border border-slate-400">
                        Revenu forfait / {{ $data->isNotEmpty() ? $data->first()->duree_contrat : '' }} mois</th>
                    <th class="custom-padding text-[12px] font-bold text-center text-slate-800 border border-slate-400">
                        Revenu</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td
                        class="custom-padding text-[12px] font-semibold text-right text-slate-800 border border-slate-400">
                        {{ $data->isNotEmpty() ? $data->first()->duree_contrat : '' }} mois
                    </td>
                    <!-- <td
                        class="custom-padding text-[12px] font-semibold text-right text-slate-800 border border-slate-400">
                        {{ $data->isNotEmpty() ? $data->first()->mois_ecoules : '' }} mois
                    </td> -->
                    <td
                        class="custom-padding text-[12px] font-semibold text-right text-slate-800 border border-slate-400">
                        {{ $data->isNotEmpty() ? $data->count() : '' }}
                    </td>
                    <td
                        class="custom-padding text-[12px] font-semibold text-right text-slate-800 border border-slate-400">
                        {{ $dataRecap->isNotEmpty() ? $dataRecap->unique('generator_id')->count() : '' }}
                    </td>
                    <td
                        class="custom-padding text-[12px] font-semibold text-right text-slate-800 border border-slate-400">
                        {{ $dataRecap->isNotEmpty() ? \App\Utils\NumberUtils::format($dataRecap->sum('forfait') ?? 0) : '' }}
                    </td>
                    <td
                        class="custom-padding text-[12px] font-semibold text-right text-slate-800 border border-slate-400">
                        {{ $dataRecap->isNotEmpty() ? \App\Utils\NumberUtils::format($dataRecap->sum('forfait') * ($data->first()->duree_contrat ?? 0)) : '' }}
                    </td>
                    <td
                        class="custom-padding text-[12px] font-semibold text-right text-slate-800 border border-slate-400">
                        {{ \App\Utils\NumberUtils::format($total2) }} FCFA
                    </td>
                </tr>
            </tbody>
        </table>
        @elseif ($generator != null)
        <h3 class="text-lg font-semibold pt-5 mb-3">Récapitulatif du Groupe Electrogène</h3>
        <table class="min-w-full border border-slate-400 divide-y divide-slate-300">
            <thead>
                <tr>
                    <th class="custom-padding text-[12px] font-bold text-center text-slate-800 border border-slate-400">
                        Activité</th>
                    <th class="custom-padding text-[12px] font-bold text-center text-slate-800 border border-slate-400">
                        Tx Occupation</th>
                    <th class="custom-padding text-[12px] font-bold text-center text-slate-800 border border-slate-400">
                        Interventions</th>
                    <th class="custom-padding text-[12px] font-bold text-center text-slate-800 border border-slate-400">
                        Revenu Contrats</th>
                    <th class="custom-padding text-[12px] font-bold text-center text-slate-800 border border-slate-400">
                        Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td
                        class="custom-padding text-[12px] font-semibold text-right text-slate-800 border border-slate-400">
                        {{ \App\Utils\NumberUtils::format((int) $workDays) }} jours
                    </td>
                    <td
                        class="custom-padding text-[12px] font-semibold text-right text-slate-800 border border-slate-400">
                        {{ \App\Utils\NumberUtils::format($txOcupation) }}%
                    </td>
                    <td
                        class="custom-padding text-[12px] font-semibold text-right text-slate-800 border border-slate-400">
                        {{ $data->isNotEmpty() ? $data->count() : '' }}
                    </td>
                    <td
                        class="custom-padding text-[12px] font-semibold text-right text-slate-800 border border-slate-400">
                        {{ \App\Utils\NumberUtils::format($revenuContrat) }}
                    </td>
                    <td
                        class="custom-padding text-[12px] font-semibold text-right text-slate-800 border border-slate-400">
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
        .info-container {
            display: none;
        }

        .custom-padding {
            padding-top: 8px;
            padding-bottom: 8px;
            padding-left: 12px;
            padding-right: 12px;
        }

        .no-print-column {
            display: none;
        }

        @media print {
            .div-table {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-self: center;
            }

            .custom-padding {
                padding: 0px !important;
            }

            .no-print-column {
                display: none !important;
            }

            .print-column {
                display: table-cell !important;
            }

            tfoot {
                display: table-footer-group;
            }

            tfoot tr {
                break-inside: avoid;
            }

            .fi-header {
                display: none;
            }

            .info-container {
                display: flex;
            }



            @page {
                margin: 20px 40px 10px 40px;
                /* top, right, bottom, left */
            }

            body {
                margin: 0;
                /* Réinitialise les marges internes */
            }
        }
    </style>
</x-filament-panels::page>