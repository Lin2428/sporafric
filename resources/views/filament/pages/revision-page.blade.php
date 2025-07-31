<x-filament::page>
    <div x-data="{ tab: 'generators' }" class="space-y-6">
        <div class="flex space-x-4 border-b">
            <button 
                x-on:click="tab = 'generators'" 
                :class="tab === 'generators' ? 'border-b-2 border-primary-600 text-primary-600' : ''"
                class="px-4 py-2">
                Groupe Electrogène
            </button>

            <button 
                x-on:click="tab = 'revisions'" 
                :class="tab === 'revisions' ? 'border-b-2 border-primary-600 text-primary-600' : ''"
                class="px-4 py-2">
                Interventions
            </button>
        </div>

        <div x-show="tab === 'generators'" wire:ignore>
            @livewire(\App\Filament\Pages\GeneratorInRevision::class)
        </div>

        <div x-show="tab === 'revisions'" wire:ignore>
            @livewire(\App\Filament\Resources\RevisionResource\Pages\ListRevisions::class)
        </div>
    </div>
</x-filament::page>