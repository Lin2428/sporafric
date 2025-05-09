<div>
    <form wire:submit="create">
        {{ $this->form }}
        <br>
        
        <x-filament::button wire:click="">
           Enrégistrer
        </x-filament::button>
    </form>
    
    <x-filament-actions::modals />
</div>
