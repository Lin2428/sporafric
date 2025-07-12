@php
    if($data == null){
        $data = collect();
    }
    $total1 = 0;

    if($data->isNotEmpty()){
        $total1 = $data->sum('devis_montant') + $data->sum('montant_piece');
    }
@endphp

<table class="min-w-full divide-y divide-slate-800">
            <thead style="display: table-row-group">
                <tr>
                    <th class="px-3 py-4 text-sm text-left font-bold text-slate-800 border border-slate-400">Date</th>
                    <th class="px-3 py-4 text-sm text-left font-bold text-slate-800 border border-slate-400">Numéro
                    </th>
                    <th class="px-3 py-4 text-sm font-bold text-left text-slate-800 border border-slate-400">Type</th>
                    <th class="px-3 py-4 text-sm font-bold text-left text-slate-800 border border-slate-400">Techniciens
                    </th>
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
                            <td class="px-3 py-4 text-sm text-left text-slate-800 border border-slate-400">{{ \App\Utils\DateUtils::format($intervention->intervention_at) }}</td>
                            <td class="px-3 font-bold py-4 text-[12px] text-left text-slate-800 border border-slate-400">
                                @php
                                 $url = '/admin/interventions/' . $intervention->id;
                                 if($intervention->devis_id != null){
                                     $url = '/admin/intervention-devis/' . $intervention->id;
                                 }
                                @endphp
                                <a target="_blank" href="{{ url($url) }}" class="text-blue-600 ">
                                    {{ $intervention->numero }}
                                </a></td>
                            <td class="px-3 py-4 text-blue-600 text-sm text-left border border-slate-400">
                                {{ \App\Enum\InterventionType::from($intervention->type_intervention)->label() }}</td>
                            <td class="px-3 py-4 text-[12px] text-left text-slate-800 border border-slate-400">
                                {{ $intervention->techniciens }}</td>
                            <td class="px-3 py-4 text-[12px] text-sm text-left text-slate-800 border border-slate-400">
                                {{ $intervention->pieces }}</td>
                            <td class="px-3 py-4 text-sm text-left text-slate-800 border border-slate-400">
                                {{ $intervention->total_pieces }}</td>
                            <td class="px-3 py-4 text-sm text-right text-slate-800 border border-slate-400">
                                {{ \App\Utils\NumberUtils::format($intervention->montant_piece) }}</td>
                            <td class="px-3 py-4 text-sm text-right text-slate-800 border border-slate-400">
                                {{ \App\Utils\NumberUtils::format($intervention->devis_montant) }}</td>
                            <td class="px-3 py-4 text-sm text-right font-bold text-slate-800 border border-slate-400">
                                {{ \App\Utils\NumberUtils::format($intervention->devis_montant + $intervention->montant_piece) }}
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
            <tfoot style="display: table-row-group">
                <tr>
                    <th colspan="5" class="text-left px-3 py-4 text-sm text-slate-800">
                        Total
                    </th>

                    <th class="px-3 py-4 text-sm text-right font-bold text-slate-800 border border-slate-400">
                        {{ \App\Utils\NumberUtils::format($data->isNotEmpty() ? $data->sum('total_pieces') : 0) }}
                    </th>
                    <th class="px-3 py-4 text-sm text-right font-bold text-slate-800 border border-slate-400">
                        {{ \App\Utils\NumberUtils::format(number: $data->isNotEmpty() ? $data->sum('montant_piece'): 0)  }}
                    </th>
                    <th class="px-3 py-4 text-sm text-right font-bold text-slate-800 border border-slate-400">
                        {{ \App\Utils\NumberUtils::format($data->isNotEmpty() ? $data->sum('devis_montant') :0)  }}
                    </th>
                    <th class="px-3 py-4 text-sm text-right font-bold text-slate-800 border border-slate-400">
                        {{ \App\Utils\NumberUtils::format($total1) }}
                    </th>
                </tr>
            </tfoot>
        </table>
