<style>
    @media print {
        @page {
            margin: 20px 40px 10px 40px;
            /* top, right, bottom, left */
        }

        body {
            margin: 0;
            /* Réinitialise les marges internes */
        }



        .no-print {
            display: none;
        }
    }

    .half {
        width: 48%;
        display: inline-block;
        vertical-align: top;
    }
</style>
@include('components.report-header')
<hr>
<div class="font-sans bg-white text-gray-900 py-8">
    <div class="text-center mb-3">
        <h1 class="text-2xl font-bold uppercase">CONTROLE RETOUR LOCATION</h1>
    </div>
    <div class=""><label class="font-bold">Identification du GE:</label></div>

    <div class="grid grid-cols-2 w-full gap-4">
        <div class="">
            <div><label class="font-bold">PUISSANCE:</label></div>
            <div><label class="font-bold">Horamètre:</label></div>
            <div><label class="font-bold">Technicien(e):</label></div>
            <div><label class="font-bold">Client /Devis:</label> </div>
            <div><label class="font-bold">Date:</label> </div>
        </div>
        <div class="text-left">
            <div><label class="font-bold">MOTEUR N° Série:</label></div>
            <div><label class="font-bold">ALTERNATEUR N° Série:</label></div>
            <div><label class="font-bold">CARTE PUPITRE N° Série:</label></div>
            <div><label class="font-bold">Inverseur:</label></div>
            <div><label class="font-bold">Visa responsable:</label></div>
        </div>
    </div>


    <div class="space-y-6">

       

        {{-- Contrôle général --}}
        <div class="mt-3">
            <h3 class="font-bold mb-2">Contrôle technique</h3>
            <div class="checkbox-wrapper-13 grid grid-cols-2 gap-2">
                @foreach ([
        'control_1' => 'Contrôle des poignées',
        'control_2' => 'Contrôle carrosserie',
        'control_3' => 'Niveau d’huile Moteur',
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
                        <input type="checkbox" id="{{ $control }}" disabled>
                        <label for="{{ $control }}">{{ $label }}</label>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Contrôle en fonctionnement --}}
        <div class="mt-6">
            <h3 class="font-bold mb-2">Contrôle en fonctionnement</h3>
            <div class="checkbox-wrapper-13 grid grid-cols-2 gap-2">
                @foreach ([
        'control_12' => 'Démarrage du GE',
        'control_13' => 'Contrôle du circuit de charge moteur',
        'control_14' => 'Contrôle ATU',
    ] as $control => $label)
                    <div>
                        <input type="checkbox" id="{{ $control }}" disabled>
                        <label for="{{ $control }}">{{ $label }}</label>
                    </div>
                @endforeach
                 <div class="">
                Fréquences (Hz) : <span class="font-bold"> 
        </div>
            </div>
        </div>

       

        {{-- Tension de sortie 230V --}}
        <div class="mt-6">
            <h3 class="font-bold mb-2">Tension de sortie (230V)</h3>
            <div class="grid grid-cols-3 gap-4">
                <input type="text" readonly value="V1n:"
                    placeholder="V1n" class="w-full font-bold border border-gray-2 p-2 rounded-lg ">
                <input type="text" readonly value="V2n: "
                    placeholder="V2n" class="w-full font-bold border border-gray-2 p-2 rounded-lg ">
                <input type="text" readonly value="V3n: "
                    placeholder="V3n" class="w-full font-bold border border-gray-2 p-2 rounded-lg ">
            </div>
        </div>

        {{-- Tension de sortie 400V --}}
        <div class="mt-6">
            <h3 class="font-bold mb-2">Tension de sortie (400V)</h3>
            <div class="grid grid-cols-3 gap-4">
                <input type="text" readonly value="U12: "
                    placeholder="U12" class="w-full font-bold border border-gray-2 p-2 rounded-lg ">
                <input type="text" readonly value="U13: "
                    placeholder="U13" class="w-full font-bold border border-gray-2 p-2 rounded-lg ">
                <input type="text" readonly value="U23: "
                    placeholder="U23" class="w-full font-bold border border-gray-2 p-2 rounded-lg ">
            </div>
        </div>

        {{-- Intensité par phase --}}
        <div class="mt-6">
            <h3 class="font-bold mb-2">Intensité par phase</h3>
            <div class="grid grid-cols-3 gap-4">
                <input type="text" readonly value="I1: "
                    placeholder="I1" class="w-full font-bold border border-gray-2 p-2 rounded-lg ">
                <input type="text" readonly value="I2: "
                    placeholder="I2" class="w-full font-bold border border-gray-2 p-2 rounded-lg ">
                <input type="text" readonly value="I3: "
                    placeholder="I3" class="w-full font-bold border border-gray-2 p-2 rounded-lg ">
            </div>
        </div>
    </div>

    {{-- Signatures --}}
    <div class="flex justify-between mt-4">
        <div class="text-center">
            <p class="mb-2 font-semibold">Signature du technicien</p>
            <div class="w-48 border-t border-gray-500 mx-auto mt-8"></div>
        </div>

        <div class="text-center">
            <p class="mb-2 font-semibold">Signature du client</p>
            <div class="w-48 border-t border-gray-500 mx-auto mt-8"></div>
        </div>
    </div>
</div>