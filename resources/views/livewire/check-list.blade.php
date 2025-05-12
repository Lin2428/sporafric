<script src="{{ asset('css/pub.css') }}"></script>

<div>
    <div class="flex justify-between">
        <x-filament::button
        color="gray"
        icon="heroicon-o-printer"
        id="print-form-etat"
    >
        Imprimer le formulaire vierge
    </x-filament::button>
        <x-filament::button wire:click="">
            Enrégistrer
         </x-filament::button>
       </div>
       <br>
    <form wire:submit="create" >
        {{ $this->form }}
        <br>
    </form>
    
  
<div id="printable" class="hidden">
    @include('impression.form-etat')
</div>
</div>


<script src="{{ asset('js/pub.js') }}"></script>
