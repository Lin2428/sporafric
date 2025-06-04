<style>
    @media print {
        @page {
            size: A4;
            margin: 1cm;
        }

        html, body {
            width: 210mm;
            height: 297mm;
            font-size: 12pt;
        }

        .no-print {
            display: none;
        }
    }
</style>

<div class="font-sans bg-white text-gray-900 px-16 py-12">
    <div class="text-center mb-18">
        <h1 class="text-3xl font-bold uppercase underline">Rapport État Avant et Après Utilisation du GE</h1>
    </div>

    <div class="mb-6 space-y-2">
        <p><span class="font-semibold">referencee du GE :</span> ______________________________________</p>
        <p><span class="font-semibold">N/S :</span> _____________________________________________</p>
    </div>

    <div class="grid grid-cols-2 gap-8">
        {{-- État avant location --}}
        <div>
            <h2 class="text-xl font-semibold mb-4 underline">État Avant Location</h2>

            <p class="mb-2"><span class="font-semibold">Technicien :</span> ________________________________</p>
            <div class="grid grid-cols-2 gap-x-6 gap-y-3 mt-4">
                @foreach ([
                    'Propre', 'Fonctionnel', 'Complet', 'Bien entretenu',
                    'Utilisable', 'Acceptable', 'Sûr', 'Légal'
                ] as $etat)
                    <label class="flex items-center space-x-2">
                        <span class="w-4 h-4 border border-gray-400 inline-block"></span>
                        <span>{{ $etat }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- État après location --}}
        <div>
            <h2 class="text-xl font-semibold mb-4 underline">État Après Location</h2>

            <p class="mb-2"><span class="font-semibold">Technicien :</span> ________________________________</p>
            <div class="grid grid-cols-2 gap-x-6 gap-y-3 mt-4">
                @foreach ([
                    'Propre', 'Fonctionnel', 'Complet', 'Bien entretenu',
                    'Utilisable', 'Acceptable', 'Sûr', 'Légal'
                ] as $etat)
                    <label class="flex items-center space-x-2">
                        <span class="w-4 h-4 border border-gray-400 inline-block"></span>
                        <span>{{ $etat }}</span>
                    </label>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Signatures --}}
    <div class="flex justify-between mt-20">
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