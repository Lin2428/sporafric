
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

    
    <div class="flex justify-between gap-2">
    <x-filament::button
    id="print-form-etat"
    color="gray"
    icon="heroicon-o-printer"
    >
    Imprimer le formulaire vierge
    </x-filament::button>

    @livewire('checklist-modal',['record' => $record] )
   
    </div>
       <br>
       
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- État avant location --}}
        <div class="border rounded-xl p-4">
            <h2 class="text-lg font-bold mb-4">État avant location</h2>

            <div class="mb-4">
                <label class="block mb-1 font-medium">Technicien(e)</label>
                <select disabled  class="w-full border rounded-xl border-gray-300 p-2">
                    @foreach($techniciens as $id => $name)
                        <option {{$id == $record->etat?->technicien_id ? 'selected' : ''}} value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block mb-1 font-medium">État</label>
                <div class="grid grid-cols-2 gap-2">
                    
                  
                        <div class="checkbox-wrapper-13">
                        <input disabled id="default-checkbox" type="checkbox" {{$record->etat?->is_clean ? 'checked' : ''}} >
                        <label for="">Propres</label>
                        </div>
                       
                    <div class="checkbox-wrapper-13">
                        <input disabled id="default-checkbox" type="checkbox" {{$record->etat?->is_functional ? 'checked' : ''}} >
                        <label for="">Démarre</label>
                        </div>

                        <div class="checkbox-wrapper-13">
                        <input disabled id="default-checkbox" type="checkbox" {{$record->etat?->is_maintained ? 'checked' : ''}} >
                        <label for="">Bien entretenu</label>
                        </div>
                </div>
            </div>

            <div class="mb-4">
                <label class="block" class>Grandeur électrique</label>
                <div class="w-full border rounded-xl p-2">
                    {{ $record->etat?->electrical_value }}
                </div>
            </div>

            <div class="mb-4">
                <label class="block">Grandeur mécanique</label>
               <div class="w-full border rounded-xl p-2">
                    {{ $record->etat?->mechanical_value }}
                </div>
            </div>

            <div class="mb-4">
                <label class="block">Nombre d'heures</label>
                <div class="w-full border rounded-xl p-2">
                    {{ $record->etat?->hour_number }}
                </div>
            </div>

            <div class="mb-4">
                <label class="block">Prochaine vidange</label>
                <div class="w-full border rounded-xl p-2">
                    {{ $record->etat?->next_vidange }}
                </div>
            </div>
        </div>

        {{-- État après location --}}
        <div class="border rounded-xl p-4">
            <h2 class="text-lg font-bold mb-4">État après location</h2>

            <div class="mb-4">
                <label >Technicien(e)</label>
                <select class="w-full border rounded-xl border-gray-300 p-2">
                    @foreach($techniciens as $id => $name)
                        <option {{$id == $record->etat?->technicien_id_after ? 'selected' : ''}} value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block mb-1 font-medium">État</label>
                <div class="grid grid-cols-2 gap-2">
                    <div class="checkbox-wrapper-13">
                        <input disabled id="default-checkbox" type="checkbox" {{$record->etat?->is_clean_after ? 'checked' : ''}} >
                        <label for="">Propres</label>
                        </div>
                    <div class="checkbox-wrapper-13">
                        <input disabled id="default-checkbox" type="checkbox" {{$record->etat?->is_functional_after ? 'checked' : ''}} >
                        <label for="">Démarre</label>
                        </div>
                    <div class="checkbox-wrapper-13">
                        <input disabled id="default-checkbox" type="checkbox" {{$record->etat?->is_maintained_after ? 'checked' : ''}} >
                        <label for="">Bien entretenu</label>
                        </div>
                </div>
            </div>

            <div class="mb-4">
                <label class="block">Grandeur électrique</label>
                <div class="w-full border rounded-xl p-2">
                    {{ $record->etat?->electrical_value_after }}
                </div>
            </div>

            <div class="mb-4">
                <label class="block">Grandeur mécanique</label>
                <div class="w-full border rounded-xl p-2">
                    {{ $record->etat?->mechanical_value_after }}
                </div>
            </div>

            <div class="mb-4">
                <label class="block">Nombre d'heures</label>
                <div class="w-full border rounded-xl p-2">
                    {{ $record->etat?->hour_number_after }}
                </div>
            </div>

            <div class="mb-4">
                <label class="block">Prochaine vidange</label>
                <div class="w-full border rounded-xl p-2">
                    {{ $record->etat?->next_vidange_after }}
                </div>
            </div>
        </div>
    </div>
        <br>
     
<div id="printable" class="hidden">
    @include('impression.form-etat')
</div>
</div>


<script src="{{ asset('js/pub.js') }}"></script>
