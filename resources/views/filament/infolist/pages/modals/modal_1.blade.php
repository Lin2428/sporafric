<x-filament::modal>
    <x-slot name="trigger">
        <x-filament::button>
            Modifier
        </x-filament::button>
    </x-slot>

    <x-slot name="heading">
        Modifier les infos de l'intervention
    </x-slot>
    <x-filament::modal id="intervention-form-modal">
        <form wire:submit.prevent="submit">
            {{ $this->form }}
            <x-filament::button type="submit">Valider</x-filament::button>
        </form>
    </x-filament::modal>
    <x-slot name="footer">
        <x-filament::button>
            Enregistrer
        </x-filament::button>
        <x-filament::button color="secondary">
            Annuler
        </x-filament::button>
    </x-slot>
</x-filament::modal>