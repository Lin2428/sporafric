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
                    <th class="px-3 py-2 text-sm text-left font-bold text-slate-800 border border-slate-400">Site
                    </th>
                                        <th class="px-3 py-2 text-sm font-bold text-left text-slate-800 border border-slate-400">Adresse</th>
                    <th class="px-3 py-2 text-sm font-bold text-left text-slate-800 border border-slate-400">Code</th>
                    <th class="px-3 py-2 text-sm font-bold text-left text-slate-800 border border-slate-400">GE</th>
                    <th class="px-3 py-2 text-sm font-bold text-left text-slate-800 border border-slate-400">P (kVA)
                    </th>
                    <th class="px-3 py-2 text-sm font-bold text-left text-slate-800 border border-slate-400">HF
                    </th>
                    <th class="px-3 py-2 text-sm font-bold text-left text-slate-800 border border-slate-400">VP
                    </th>
                    <th class="px-3 py-2 text-sm font-bold text-left text-slate-800 border border-slate-400">TAV
                    </th>
                </tr>
            </thead>
            <tbody>

                @if ($data->isNotEmpty())
                    @foreach ($data as $item)
                        <tr class="border-b border-slate-400">
                            <td class="px-3 py-2 text-blue-600 text-[12px] text-left border border-slate-400">
                                {{$item->contractGenerator?->site ?? $item->devisGenerator?->devis?->site}}
                            </td>
                            <td class="px-3 py-2 text-[12px] text-slate-800 border border-slate-400">
                                {{$item->adresse }}
                            </td>
                            <td class="px-3 py-2 text-[12px] text-left text-slate-800 border border-slate-400">
                                {{ $item->contractGenerator?->code_site ?? $item->devisGenerator?->devis?->code_site }}
                            </td>
                            
                            <td class="px-3 py-2 text-[12px] text-left text-slate-800 border border-slate-400">
                                    {{ $item->name }}
                             </td>
                            <td class="px-3 py-2 text-[12px] text-right text-slate-800 border border-slate-400">
                                {{$item->power }}
                            </td>
                            <td class="px-3 py-2 text-[12px] text-right text-slate-800 border border-slate-400">
                                
                            </td>
                              <td class="px-3 py-2 text-[12px] text-right text-slate-800 border border-slate-400">
                                {{$item->prochain_visite }}
                            </td>
                              <td class="px-3 py-2 text-[12px] text-right text-slate-800 border border-slate-400">
                                {{$item->next_vidange }}
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
</x-filament-panels::page>
