<x-filament-panels::page>
    <form wire:submit.prevent="submit" class="space-y-4">
        {{ $this->form }}
    </form>
    <x-filament::button type="submit" color="primary" class="hidden">
        Envoyer
    </x-filament::button>

    <table class="table-auto border divide-y divide-x">
        <thead class="border">
            <tr>
                <th class="border px-4 py-2 text-left text-sm font-medium">Date</th>
                <th class="border px-4 py-4 text-left text-sm font-medium">Bon d’intervention</th>
                <th class="border px-4 py-2 text-left text-sm font-medium">Code Site</th>
                <th class="border px-4 py-2 text-left text-sm font-medium">Type d'intervention</th>
                <th class="border px-4 py-2 text-left text-sm font-medium">Status</th>
                <th class="border px-4 py-2 text-left text-sm font-medium">Description</th>
            </tr>
        </thead>
        <tbody class="divide-e divide-y  divide-x">
            @if ($this->results!= null)
            @foreach ($this->results as $intervention)
            <tr class=" transition">
                <td class="border px-4 py-2 text-sm ">{{ $intervention->created_at->format('d/m/Y') }}</td>
                <td class="border px-4 py-2 text-sm ">{{ $intervention->identifiant }}</td>
                <td class="border px-4 py-2 text-sm ">{{ $intervention->contract?->code_site }}</td>
                <td class="border px-4 py-2 text-sm ">{{
                    \App\Enum\InterventionType::from($intervention->type)->label()}}
                </td>
                <td class="border px-4 py-2 text-sm">{{
                    \App\Enum\InterventionStatus::from($intervention->status)->label()}}</td>
                <td class="border px-4 py-2 text-sm  max-w-xs truncate" title="{{ $intervention->description_panne }}">
                    {{ $intervention->description_panne }}
                </td>
            </tr>
            @endforeach
            @endif
        </tbody>
    </table>
</x-filament-panels::page>