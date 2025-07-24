
<script src="{{ asset('css/pub.css') }}"></script>

<div>

<style>
  @supports (-webkit-appearance: none) or (-moz-appearance: none) {
    .checkbox-wrapper-13 input[type=checkbox] {
      --active: #f8c11a;
      --active-inner: #fff;
      --focus: 2px rgba(254, 215, 39, 0.3);
      --border: #e1d9bb;
      --border-hover: #ffcf40;
      --background: #fff;
      --disabled: #c1920426;
      --disabled-inner: #f8c11a;
      -webkit-appearance: none;
      -moz-appearance: none;
      height: 21px;
      outline: none;
      display: inline-block;
      vertical-align: top;
      position: relative;
      margin: 0;
      cursor: pointer;
      border: 1px solid var(--bc, var(--border));
      background: var(--b, var(--background));
      transition: background 0.3s, border-color 0.3s, box-shadow 0.2s;
    }
    .checkbox-wrapper-13 input[type=checkbox]:after {
      content: "";
      display: block;
      left: 0;
      top: 0;
      position: absolute;
      transition: transform var(--d-t, 0.3s) var(--d-t-e, ease), opacity var(--d-o, 0.2s);
    }
    .checkbox-wrapper-13 input[type=checkbox]:checked {
      --b: var(--active);
      --bc: var(--active);
      --d-o: .3s;
      --d-t: .6s;
      --d-t-e: cubic-bezier(.2, .85, .32, 1.2);
    }
    .checkbox-wrapper-13 input[type=checkbox]:disabled {
      --b: var(--disabled);
      cursor: not-allowed;
      opacity: 0.9;
    }
    .checkbox-wrapper-13 input[type=checkbox]:disabled:checked {
      --b: var(--disabled-inner);
      --bc: var(--border);
    }
    .checkbox-wrapper-13 input[type=checkbox]:disabled + label {
      cursor: not-allowed;
    }
    .checkbox-wrapper-13 input[type=checkbox]:hover:not(:checked):not(:disabled) {
      --bc: var(--border-hover);
    }
    .checkbox-wrapper-13 input[type=checkbox]:focus {
      box-shadow: 0 0 0 var(--focus);
    }
    .checkbox-wrapper-13 input[type=checkbox]:not(.switch) {
      width: 21px;
    }
    .checkbox-wrapper-13 input[type=checkbox]:not(.switch):after {
      opacity: var(--o, 0);
    }
    .checkbox-wrapper-13 input[type=checkbox]:not(.switch):checked {
      --o: 1;
    }
    .checkbox-wrapper-13 input[type=checkbox] + label {
      display: inline-block;
      vertical-align: middle;
      cursor: pointer;
      margin-left: 4px;
    }

    .checkbox-wrapper-13 input[type=checkbox]:not(.switch) {
      border-radius: 7px;
    }
    .checkbox-wrapper-13 input[type=checkbox]:not(.switch):after {
      width: 5px;
      height: 9px;
      border: 2px solid var(--active-inner);
      border-top: 0;
      border-left: 0;
      left: 7px;
      top: 4px;
      transform: rotate(var(--r, 20deg));
    }
    .checkbox-wrapper-13 input[type=checkbox]:not(.switch):checked {
      --r: 43deg;
    }
  }

  .checkbox-wrapper-13 * {
    box-sizing: inherit;
  }
  .checkbox-wrapper-13 *:before,
  .checkbox-wrapper-13 *:after {
    box-sizing: inherit;
  }
</style>

    
    <div class="flex justify-end gap-2">
    @livewire('checklist-modal',['record' => $record] )
       <x-filament::button
    id="print-form-etat"
    color="info"
    icon="heroicon-o-printer"
    >
    Imprimer
    </x-filament::button>

    <x-filament::button
    id="print-form-etat-vide"
    color="gray"
    icon="heroicon-o-printer"
    >
    Imprimer le formulaire vierge
    </x-filament::button>


   
    </div>
       <br>
       
    <div class="p-6 space-y-6">

    <h2 class="text-xl font-bold">CONTROLE RETOUR LOCATION</h2>

    <p><span class="font-semibold">Client/Devis:</span> {{ $record->etat?->devis?->customer?->name }} / {{$record->etat?->devis?->number}}</p>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block font-semibold mb-1">Technicien(e)</label>
            <input type="text" value="{{ $record->etat?->technicien?->name }}" readonly class="w-full border border-gray-2 p-2 rounded-lg ">
        </div>

        <div>
            <label class="block font-semibold mb-1">Visa responsable</label>
            <input type="text" value="{{ $record->etat?->responsable }}" readonly class="w-full border border-gray-2 p-2 rounded-lg ">
        </div>
    </div>

    {{-- Contrôle général --}}
    <div class="mt-6">
        <h3 class="font-bold mb-2">Contrôle technique</h3>
        <div class="checkbox-wrapper-13 grid grid-cols-2 gap-2">
            @foreach([
                'control_1' => 'Contrôle des poignées',
                'control_2' => 'Contrôle carrosserie',
                'control_3' => "Niveau d’huile Moteur",
                'control_4' => 'Niveau du Liquide de Refroidissement',
                'control_5' => 'Contrôle du filtre à huile',
                'control_6' => 'Contrôle du filtre à air',
                'control_7' => 'Contrôle du filtre à carburant',
                'control_8' => 'Contrôle du circuit carburant',
                'control_9' => 'Contrôle du circuit de refroidissement',
                'control_10' => 'Contrôle de l’état des courroies',
                'control_11' => 'Contrôle de charge de batterie',
            ] as $control => $label)
                <div>
                    <input type="checkbox" id="{{ $control }}" disabled {{ $record->etat?->$control ? 'checked' : '' }}>
                    <label for="{{ $control }}">{{ $label }}</label>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Contrôle en fonctionnement --}}
    <div class="mt-6">
        <h3 class="font-bold mb-2">Contrôle en fonctionnement</h3>
        <div class="checkbox-wrapper-13 grid grid-cols-2 gap-2">
            @foreach([
                'control_12' => 'Démarrage du GE',
                'control_13' => 'Contrôle du circuit de charge moteur',
                'control_14' => 'Contrôle ATU',
            ] as $control => $label)
                <div>
                    <input type="checkbox" id="{{ $control }}" disabled {{ $record->etat?->$control ? 'checked' : '' }}>
                    <label for="{{ $control }}">{{ $label }}</label>
                </div>
                
            @endforeach
             <div>
            <span class="block font-semibold mb-1">Fréquences (Hz) : {{ $record->etat?->control_frequence }}</span>
        </div>
        </div>
    </div>

    {{-- Tension de sortie 230V --}}
    <div class="mt-6">
        <h3 class="font-bold mb-2">Tension de sortie (230V)</h3>
        <div class="grid grid-cols-3 gap-4">
            <input type="text" readonly value="{{ $record->etat?->control_tension['v1'] ?? '' }}" placeholder="V1n" class="w-full border border-gray-2 p-2 rounded-lg ">
            <input type="text" readonly value="{{ $record->etat?->control_tension['v2'] ?? '' }}" placeholder="V2n" class="w-full border border-gray-2 p-2 rounded-lg ">
            <input type="text" readonly value="{{ $record->etat?->control_tension['v3'] ?? '' }}" placeholder="V3n" class="w-full border border-gray-2 p-2 rounded-lg ">
        </div>
    </div>

    {{-- Tension de sortie 400V --}}
    <div class="mt-6">
        <h3 class="font-bold mb-2">Tension de sortie (400V)</h3>
        <div class="grid grid-cols-3 gap-4">
            <input type="text" readonly value="{{ $record->etat?->control_tension_2['u1'] ?? '' }}" placeholder="U12" class="w-full border border-gray-2 p-2 rounded-lg ">
            <input type="text" readonly value="{{ $record->etat?->control_tension_2['u2'] ?? '' }}" placeholder="U13" class="w-full border border-gray-2 p-2 rounded-lg ">
            <input type="text" readonly value="{{ $record->etat?->control_tension_2['u3'] ?? '' }}" placeholder="U23" class="w-full border border-gray-2 p-2 rounded-lg ">
        </div>
    </div>

    {{-- Intensité par phase --}}
    <div class="mt-6">
        <h3 class="font-bold mb-2">Intensité par phase</h3>
        <div class="grid grid-cols-3 gap-4">
            <input type="text" readonly value="{{ $record->etat?->control_intensite['i1'] ?? '' }}" placeholder="I1" class="w-full border border-gray-2 p-2 rounded-lg ">
            <input type="text" readonly value="{{ $record->etat?->control_intensite['i2'] ?? '' }}" placeholder="I2" class="w-full border border-gray-2 p-2 rounded-lg ">
            <input type="text" readonly value="{{ $record->etat?->control_intensite['i3'] ?? '' }}" placeholder="I3" class="w-full border border-gray-2 p-2 rounded-lg ">
        </div>
    </div>
</div>
     
<div id="printable" class="hidden">
    @include('impression.form-etat')
</div>
<div id="printable_vide" class="hidden">
    @include('impression.form-etat-vide')
</div>
</div>


<script src="{{ asset('js/pub.js') }}"></script>
