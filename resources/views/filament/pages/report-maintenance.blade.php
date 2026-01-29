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
    $rowTotal = 12;

    //variable d'impression de colonne
$printDebut = !in_array('start_at', $printableFields);
$printFin = !in_array('end_at', $printableFields);
$printSite = !in_array('site', $printableFields);
$printTech = !in_array('technicien', $printableFields);
$printGen = !in_array('generator', $printableFields);
$printPiece = !in_array('piece', $printableFields);
$printQtyPiece = !in_array('qty_piece', $printableFields);
$printMPieces = !in_array('amount_pieces', $printableFields);
$printMInt = !in_array('amount_intervention', $printableFields);
$printMTotal = !in_array('amount_total', $printableFields);

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
        <div class="overflow-x-auto">
            <table class="w-max divide-y divide-slate-800 ">
                <thead style="display: table-row-group">
                    <tr>
                        {{-- <th class="px-3 py-2 text-[12px] text-left font-bold text-slate-800 border border-slate-400">Date</th> --}}
                        <th
                            class="px-3 py-2 text-[12px] text-left font-bold text-slate-800 border border-slate-400 whitespace-nowrap">
                            N°
                        </th>
                        <th
                            class="px-3 py-2 text-[12px] text-left font-bold text-slate-800 border border-slate-400 max-w-[110px]">
                            N° BT
                        </th>
                        <th
                            class="px-3 py-2 text-[12px] text-left font-bold text-slate-800 border border-slate-400 whitespace-nowrap">
                            Devis
                        </th>
                        <th
                            class="px-3 py-2 text-[12px] font-bold text-left text-slate-800 border border-slate-400 max-w-[100px]">
                            Type
                        </th>
                        <th
                            class="px-3 py-2 text-[12px] font-bold text-left text-slate-800 border border-slate-400 whitespace-nowrap">
                            Date planifiée
                        </th>
                        <th
                            class="start_at px-3 py-2 text-[12px] font-bold text-left text-red-600 border border-slate-400 whitespace-nowrap {{ $printDebut ? 'no-print-column' : '' }}">
                            Début
                        </th>
                        <th
                            class="end_at px-3 py-2 text-[12px] font-bold text-left text-red-600 border border-slate-400 whitespace-nowrap {{ $printFin ? 'no-print-column' : '' }}">
                            Fin
                        </th>
                        <th
                            class="site px-3 py-2 text-[12px] font-bold text-left text-red-600 border border-slate-400 max-w-[150px] {{ $printSite ? 'no-print-column' : '' }}">
                            Site
                        </th>
                        <th
                            class="technicien px-3 py-2 text-[12px] text-left font-bold text-red-600 border border-slate-400 max-w-[150px] {{ $printTech ? 'no-print-column' : '' }}">
                            Techniciens
                        </th>
                        <th
                            class="generator px-3 py-2 text-[12px] text-left font-bold text-red-600 border border-slate-400 whitespace-nowrap {{ $printGen ? 'no-print-column' : '' }}">
                            GE
                        </th>
                        <th
                            class="px-3 py-2 text-[12px] text-left font-bold text-red-600 border border-slate-400 whitespace-nowrap {{ $printGen ? 'no-print-column' : '' }}">
                            Nb hr
                        </th>
                        <th
                            class="piece px-3 py-2 text-[12px] text-left font-bold text-red-600 border border-slate-400 max-w-[400px] {{ $printPiece ? 'no-print-column' : '' }}">
                            Pièces
                        </th>
                        <th
                            class="qty_piece px-3 py-2 text-[12px] font-bold text-left text-red-600 border border-slate-400 whitespace-nowrap {{ $printQtyPiece ? 'no-print-column' : '' }}">
                            QT Pièces
                        </th>
                        <th
                            class="amount_pieces px-3 py-2 text-[12px] font-bold text-left text-red-600 border border-slate-400 whitespace-nowrap {{ $printMPieces ? 'no-print-column' : '' }}">
                            Prix
                            Total</th>
                        <th
                            class="amount_intervention px-3 py-2 text-[12px] font-bold text-left text-red-600 border border-slate-400 whitespace-nowrap {{ $printMInt ? 'no-print-column' : '' }}">
                            M.
                            Int</th>
                        <th
                            class="total_mount px-3 py-2 text-[12px] font-bold text-left text-red-600 border border-slate-400 whitespace-nowrap {{ $printMTotal ? 'no-print-column' : '' }}">
                            M. Total
                        </th>
                    </tr>
                </thead>
                <tbody>

                    @if ($data->isNotEmpty())
                        @foreach ($data as $intervention)
                            <tr class="border-b border-slate-400">
                                {{-- <td class="px-3 py-2 text-[12px] text-left text-slate-800 border border-slate-400">{{ \App\Utils\DateUtils::format($intervention->date) }}</td> --}}
                                <td
                                    class="px-3 py-2 text-[12px] text-left text-slate-800 border border-slate-400 whitespace-nowrap">
                                    <a target="_blank" href="{{ url('/admin/interventions/' . $intervention->id) }}"
                                        class="text-blue-600 ">
                                        #{{ $intervention->numero }}
                                    </a>
                                </td>
                                <td
                                    class="px-3 py-2 text-[12px] text-left text-slate-800 border border-slate-400 max-w-[110px]">
                                    {{ $intervention->identifiant }}
                                </td>
                                <td
                                    class="px-3 py-2 text-[12px] text-left text-slate-800 border border-slate-400 whitespace-nowrap">
                                    {{ $intervention->numero_devis }}
                                </td>
                                <td
                                    class="px-3 py-2 text-blue-600 text-[12px] text-left border border-slate-400 max-w-[100px]">
                                    {{ \App\Enum\InterventionType::from($intervention->type_intervention)->label() }}
                                </td>
                                <td class="px-3 py-2 text-[12px] text-left border border-slate-400 whitespace-nowrap">
                                    {{ \App\Utils\DateUtils::formatWithTime($intervention->intervention_at) }}
                                </td>
                                <td
                                    class="px-3 py-2 text-[12px] text-left text-slate-800 border border-slate-400 whitespace-nowrap {{ $printDebut ? 'no-print-column' : '' }}">
                                    {{ \App\Utils\DateUtils::formatWithTime($intervention->interevention_start_at) }}
                                </td>
                                <td
                                    class="px-3 py-2 text-[12px] text-left text-slate-800 border border-slate-400 whitespace-nowrap {{ $printFin ? 'no-print-column' : '' }}">
                                    {{ \App\Utils\DateUtils::formatWithTime($intervention->interevention_end_at) }}
                                </td>
                                <td
                                    class="px-3 py-2 text-[12px] text-left text-slate-800 border border-slate-400 max-w-[120px] {{ $printSite ? 'no-print-column' : '' }}">
                                    {{ $intervention->site }}
                                </td>
                                <td
                                    class="px-3 py-2 text-[12px] text-left text-slate-800 border border-slate-400 max-w-[150px] {{ $printTech ? 'no-print-column' : '' }}">
                                    {{ $intervention->techniciens }}
                                </td>
                                <td
                                    class="px-3 py-2 text-[12px] text-left text-slate-800 border border-slate-400 whitespace-nowrap {{ $printGen ? 'no-print-column' : '' }}">
                                    {{ $intervention->generator_name }}
                                </td>
                                <td
                                    class="px-3 py-2 text-[12px] text-left text-slate-800 border border-slate-400 whitespace-nowrap {{ $printGen ? 'no-print-column' : '' }}">
                                    {{ $intervention->generator_houres }}
                                </td>
                                <td
                                    class="px-3 py-2 text-[12px] text-left text-slate-800 border border-slate-400 max-w-[400px] {{ $printPiece ? 'no-print-column' : '' }}">
                                    {{ $intervention->pieces }}
                                </td>
                                <td
                                    class="px-3 py-2 text-[12px] text-left text-slate-800 border border-slate-400 whitespace-nowrap {{ $printQtyPiece ? 'no-print-column' : '' }}">
                                    {{ $intervention->total_pieces }}</td>
                                <td
                                    class="px-3 py-2 text-[12px] text-right text-slate-800 border border-slate-400 whitespace-nowrap {{ $printMPieces ? 'no-print-column' : '' }}">
                                    {{ \App\Utils\NumberUtils::format($intervention->montant_piece) }}</td>
                                <td
                                    class="px-3 py-2 text-[12px] text-right text-slate-800 border border-slate-400 whitespace-nowrap {{ $printMInt ? 'no-print-column' : '' }}">
                                    {{ \App\Utils\NumberUtils::format($intervention->montant) }}</td>
                                <td
                                    class="px-3 py-2 text-[12px] text-right font-bold text-slate-800 border border-slate-400 whitespace-nowrap {{ $printMTotal ? 'no-print-column' : '' }}">
                                    {{ \App\Utils\NumberUtils::format($intervention->montant + $intervention->montant_piece) }}
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
                @if (!$printMTotal || !$printMInt || !$printMPieces || !$printQtyPiece)
                    <tfoot style="display: table-row-group">
                        <tr>
                            <th colspan="{{ $rowTotal }}" class="text-left px-3 py-2 text-[12px] text-slate-800">
                                Total
                            </th>
                            @if (!$printQtyPiece)
                                <th
                                    class="px-3 py-2 text-[12px] text-right font-bold text-slate-800 border border-slate-400">
                                    {{ \App\Utils\NumberUtils::format($data->isNotEmpty() ? $data->sum('total_pieces') : 0) }}
                                </th>
                            @endif
                            @if (!$printMPieces)
                                <th
                                    class="px-3 py-2 text-[12px] text-right font-bold text-slate-800 border border-slate-400">
                                    {{ \App\Utils\NumberUtils::format(number: $data->isNotEmpty() ? $data->sum('montant_piece') : 0) }}
                                </th>
                            @endif
                            @if (!$printMInt)
                                <th
                                    class="px-3 py-2 text-[12px] text-right font-bold text-slate-800 border border-slate-400">
                                    {{ \App\Utils\NumberUtils::format($data->isNotEmpty() ? $data->sum('montant') : 0) }}
                                </th>
                            @endif
                            @if (!$printMTotal)
                                <th
                                    class="px-3 py-2 text-[12px] text-right font-bold text-slate-800 border border-slate-400">
                                    {{ \App\Utils\NumberUtils::format($total1) }}
                                </th>
                            @endif
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        @if ($type === '0')
            <h3 class="text-lg font-semibold pt-5 mb-3">Récaputilatif du contrat</h3>
            <table class="min-w-full border border-slate-400 divide-y divide-slate-300">
                <thead>
                    <tr>
                        <th class="px-3 py-2 text-[12px] font-bold text-center text-slate-800 border border-slate-400">
                            Durée
                        </th>
                        <th class="px-3 py-2 text-[12px] font-bold text-center text-slate-800 border border-slate-400">
                            Ecoulés
                        </th>
                        <th class="px-3 py-2 text-[12px] font-bold text-center text-slate-800 border border-slate-400">
                            Interventions</th>
                        <th class="px-3 py-2 text-[12px] font-bold text-center text-slate-800 border border-slate-400">
                            Nombre GE</th>
                        <th class="px-3 py-2 text-[12px] font-bold text-center text-slate-800 border border-slate-400">
                            Forfait
                        </th>
                        <th class="px-3 py-2 text-[12px] font-bold text-center text-slate-800 border border-slate-400">
                            Payé</th>
                        <th class="px-3 py-2 text-[12px] font-bold text-center text-slate-800 border border-slate-400">
                            Revenu
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td
                            class="px-3 py-2 text-[12px] font-semibold text-right text-slate-800 border border-slate-400">
                            {{ $data->isNotEmpty() ? $data->first()->duree_contrat : '' }} mois
                        </td>
                        <td
                            class="px-3 py-2 text-[12px] font-semibold text-right text-slate-800 border border-slate-400">
                            {{ $data->isNotEmpty() ? $data->first()->mois_ecoules : '' }} mois
                        </td>
                        <td
                            class="px-3 py-2 text-[12px] font-semibold text-right text-slate-800 border border-slate-400">
                            {{ $data->isNotEmpty() ? $data->count() : '' }}
                        </td>
                        <td
                            class="px-3 py-2 text-[12px] font-semibold text-right text-slate-800 border border-slate-400">
                            @php
                                if ($data->isNotEmpty()) {
                                    $count = $data->unique('generator_id')->count();
                                    $countNewGenerator =
                                        $data->whereNotNull('new_generator_id')->unique('new_generator_id')->count() ??
                                        0;
                                    $count -= $countNewGenerator;
                                }
                            @endphp
                            {{ $data->isNotEmpty() ? $count : '' }}
                        </td>
                        <td
                            class="px-3 py-2 text-[12px] font-semibold text-right text-slate-800 border border-slate-400">
                            {{ $data->isNotEmpty() ? \App\Utils\NumberUtils::format($forfait) : '' }}
                        </td>
                        <td
                            class="px-3 py-2 text-[12px] font-semibold text-right text-slate-800 border border-slate-400">
                            {{ $data->isNotEmpty() ? \App\Utils\NumberUtils::format($revenuContrat) : '' }}
                        </td>
                        <td
                            class="px-3 py-2 text-[12px] font-semibold text-right text-slate-800 border border-slate-400">
                            {{ \App\Utils\NumberUtils::format($total2) }} FCFA
                        </td>
                    </tr>
                </tbody>
            </table>
        @endif


        @if ($type === '1')
            <h3 class="text-lg font-semibold pt-5 mb-3">Récaputilatif du Groupe Electrogène</h3>
            <table class="min-w-full border border-slate-400 divide-y divide-slate-300">
                <thead>
                    <tr>
                        <th class="px-3 py-2 text-[12px] font-bold text-center text-slate-800 border border-slate-400">
                            Activité
                        </th>
                        <th class="px-3 py-2 text-[12px] font-bold text-center text-slate-800 border border-slate-400">
                            Tx Occupation
                        </th>
                        <th class="px-3 py-2 text-[12px] font-bold text-center text-slate-800 border border-slate-400">
                            Interventions

                        </th>
                        <th class="px-3 py-2 text-[12px] font-bold text-center text-slate-800 border border-slate-400">
                            Revenu Contrats
                        </th>
                        <th class="px-3 py-2 text-[12px] font-bold text-center text-slate-800 border border-slate-400">
                            Total
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td
                            class="px-3 py-2 text-[12px] font-semibold text-right text-slate-800 border border-slate-400">
                            {{ \App\Utils\NumberUtils::format((int) $workDays) }} jours
                        </td>
                        <td
                            class="px-3 py-2 text-[12px] font-semibold text-right text-slate-800 border border-slate-400">
                            {{ \App\Utils\NumberUtils::format($txOcupation) }}%
                        </td>
                        <td
                            class="px-3 py-2 text-[12px] font-semibold text-right text-slate-800 border border-slate-400">
                            {{ $data->isNotEmpty() ? $data->count() : '' }}
                        </td>
                        <td
                            class="px-3 py-2 text-[12px] font-semibold text-right text-slate-800 border border-slate-400">
                            {{ \App\Utils\NumberUtils::format($revenuContrat) }}
                        </td>
                        <td
                            class="px-3 py-2 text-[12px] font-semibold text-right text-slate-800 border border-slate-400">
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

        .no-print-column {
            display: none;
        }

        @media print {
            tfoot {
                display: table-footer-group;
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
