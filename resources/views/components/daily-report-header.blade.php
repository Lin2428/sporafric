@props([
    'title',
    'contracts',
])

<div class="flex justify-between pb-4">
    <div>
        <h1 class="text-2xl font-bold mb-2">{{ $title }}</h1>
        <div class="flex gap-8">
            <div class="flex items-center gap-4">
                <span class="text-sm">Contrat :</span>
                <div class="w-[300px]">
                    {{ $this->form }}
                </div>
            </div>

            <div class="flex items-center gap-4">
                <span class="text-sm">Par : </span>
                <div class="no-print">
                    <x-filament::input.wrapper>
                        <x-filament::input.select wire:model.live="type" searchable>
                            <option value="0">Contrat</option>
                            <option value="1">Groupe électrogene</option>
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>
            </div>
        </div>
    </div>
    <div class="text-sm text-slate-500">
        <p class="text-sm">Générer le : <em
                class="text-slate-600 font-semibold">{{ now()->timezone('Africa/Brazzaville')->format('d/m/Y H:i:s') }}</em></p>
        <p>Imprimé par : <em class="text-slate-600 font-semibold">{{ auth()->user()->name  }}</em></p>
    </div>
</div>
