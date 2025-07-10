<x-filament-panels::page>
<form action="">
    {{ $this->form }}
    <br>
<x-filament::button wire:click="submit" class="mt-4">
        Mettre à jour
    </x-filament::button>
</form>
</x-filament-panels::page>
