@php
    if($data == null){
        $data = collect();
    }
@endphp

<table class="min-w-full divide-y divide-slate-800">
            <thead style="display: table-row-group">
                <tr>
                    <th class="px-3 py-4 text-sm text-left font-bold text-slate-800 border border-slate-400">Date livraison</th>
                    <th class="px-3 py-4 text-sm text-left font-bold text-slate-800 border border-slate-400">Numéro
                    </th>
                    <th class="px-3 py-4 text-sm font-bold text-left text-slate-800 border border-slate-400">Client</th>
                    <th class="px-3 py-4 text-sm font-bold text-left text-slate-800 border border-slate-400">Telephone</th>
                    <th class="px-3 py-4 text-sm font-bold text-left text-slate-800 border border-slate-400">Coût(FCFA)
                    </th>
                    <th class="px-3 py-4 text-sm text-left font-bold text-slate-800 border border-slate-400">Date retour
                    </th>
                </tr>
            </thead>
            <tbody>

                @if ($data->isNotEmpty())
                    @foreach ($data as $devis)
                        <tr class="border-b border-slate-400">
                            <td class="px-3 py-4 text-sm text-left text-slate-800 border border-slate-400">{{ \App\Utils\DateUtils::format($devis->devis->start_date) }}</td>
                            <td class="px-3 font-bold py-4 text-sm text-left text-slate-800 border border-slate-400">
                                <a target="_blank" href="{{ url('/admin/devis/' . $devis->devis->id) }}" class="text-blue-600 ">
                                    {{ $devis->devis->number }}
                                </a></td>
                            <td class="px-3 py-4 text-blue-600 text-sm text-left border border-slate-400">
                                {{$devis->devis->customer->name}} {{$devis->devis->customer_name}}</td>
                            <td class="px-3 py-4 text-[12px] text-left text-slate-800 border border-slate-400">
                                {{ $devis->devis->customer->phone }}</td>
                            <td class="px-3 py-4 font-bold text-sm text-right text-slate-800 border border-slate-400">
                                {{ \App\Utils\NumberUtils::format($devis->devis->forfait) }}</td>
                           
                            <td class="px-3 py-4 text-sm text-right text-slate-800 border border-slate-400">
                                {{ $devis->devis?->end_date ? \App\Utils\DateUtils::format($devis->devis->end_date) : null }}
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>