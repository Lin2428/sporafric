<script src="{{ asset('css/pub.css') }}"></script>

<div>
    <div class="flex justify-between gap-2">
    <x-filament::button
    id="print-form-etat"
    color="gray"
    icon="heroicon-o-printer"
    >
    Imprimer le formulaire vierge
    </x-filament::button>

    <x-filament::button
    wire:click="submit"
>
    Enregistrer
    </x-filament::button>
   
    </div>
       <br>
        {{ $this->form }}
        <br>
     
<div id="printable" class="hidden">
    @include('impression.form-etat')
</div>
</div>


<script src="{{ asset('js/pub.js') }}"></script>
