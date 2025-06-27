@php
    if($data == null){
        $data = collect();
    }
@endphp

<x-filament-panels::page>

{{$this->form}}


<table class="min-w-full divide-y divide-slate-800">
            <thead style="display: table-row-group">
                <tr>
                    <th class="px-3 py-4 text-sm text-left font-bold text-slate-800 border border-slate-400">Client</th>
                    <th class="px-3 py-4 text-sm text-left font-bold text-slate-800 border border-slate-400">Site
                    </th>
                    <th class="px-3 py-4 text-sm font-bold text-left text-slate-800 border border-slate-400">Code</th>
                    <th class="px-3 py-4 text-sm font-bold text-left text-slate-800 border border-slate-400">GE</th>
                    <th class="px-3 py-4 text-sm font-bold text-left text-slate-800 border border-slate-400">P (KVA)
                    </th>
                </tr>
            </thead>
            <tbody>

                @if ($data->isNotEmpty())
                    @foreach ($data as $item)
                        <tr class="border-b border-slate-400">
                            <td class="px-3 py-4 text-sm text-left text-slate-800 border border-slate-400">{{$item->contractGenerator?->contract?->customer?->name ?? $item->devisGenerator?->devis?->customer?->name }} {{$item->devisGenerator?->devis?->customer_name}}</td>
                            <td class="px-3 py-4 text-blue-600 text-sm text-left border border-slate-400">
                                {{$item->contractGenerator?->site ?? $item->devisGenerator?->devis?->site}}</td>
                            <td class="px-3 py-4 text-[12px] text-left text-slate-800 border border-slate-400">
                                {{ $item->contractGenerator?->code_site ?? $item->devisGenerator?->devis?->code_site }}</td>
                                 <td class="px-3 py-4 text-sm text-left text-slate-800 border border-slate-400">
                                    {{ $item->name }}
                             </td>
                            <td class="px-3 py-4 text-sm text-right text-slate-800 border border-slate-400">
                                {{$item->power }}</td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
</x-filament-panels::page>
