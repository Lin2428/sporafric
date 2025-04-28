<x-filament-panels::page>
    <form wire:submit.prevent="submit" class="space-y-4">
        {{ $this->form }}
    </form>
    <x-filament::button type="submit" color="primary" class="hidden">
        Envoyer
    </x-filament::button>

    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-blue-600 text-white">
            <tr>
                <th class="px-4 py-2 text-left text-sm font-medium">Date</th>
                <th class="px-4 py-4 text-left text-sm font-medium">Bon d’intervention</th>
                <th class="px-4 py-2 text-left text-sm font-medium">Code Site</th>
                <th class="px-4 py-2 text-left text-sm font-medium">Type d'intervention</th>
                <th class="px-4 py-2 text-left text-sm font-medium">Status</th>
                <th class="px-4 py-2 text-left text-sm font-medium">Description</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100">
            @if ($this->results!= null)
            @foreach ($this->results as $intervention)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-4 py-2 text-sm text-gray-700">{{ $intervention->created_at->format('d/m/Y') }}</td>
                <td class="px-4 py-2 text-sm text-gray-700">{{ $intervention->identifiant }}</td>
                <td class="px-4 py-2 text-sm text-gray-700">{{ $intervention->contract->code_site }}</td>
                <td class="px-4 py-2 text-sm text-gray-700">{{
                    \App\Enum\InterventionType::from($intervention->type)->label()}}
                </td>
                <td class="px-4 py-2 text-sm text-gray-700">{{
                    \App\Enum\InterventionStatus::from($intervention->status)->label()}}</td>
                <td class="px-4 py-2 text-sm text-gray-700 max-w-xs truncate" title="{{ $intervention->description }}">
                    {{ $intervention->description_panne }}
                </td>
            </tr>
            @endforeach
            @endif
        </tbody>
    </table>
</x-filament-panels::page>