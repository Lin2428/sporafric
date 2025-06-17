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
        <p><span class="font-semibold">Référence du GE :</span> {{ $reference ?? '____________________________' }}</p>
        <p><span class="font-semibold">N/S :</span> {{ $ns ?? '____________________________' }}</p>
    </div>

    <div class="grid grid-cols-2 gap-8">
        {{-- État avant location --}}
        <div>
            <h2 class="text-xl font-semibold mb-4 underline">État Avant Location</h2>

            <p class="mb-2"><span class="font-semibold">Technicien :</span> {{ $technician_before_name ?? '____________________________' }}</p>

            <div class="grid grid-cols-2 gap-x-6 gap-y-3 mt-4">
                @foreach(['Propre' => 'is_clean', 'Démarre' => 'is_functional', 'Bien entretenu' => 'is_maintained'] as $label => $key)
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" disabled 
                            @if(isset($etat_before) && in_array($key, $etat_before)) checked @endif
                            class="w-4 h-4 border border-gray-400">
                        <span>{{ $label }}</span>
                    </label>
                @endforeach
            </div>

            <p><span class="font-semibold">Grandeur électrique :</span> {{ $electrical_value_before ?? '________' }}</p>
            <p><span class="font-semibold">Grandeur mécanique :</span> {{ $mechanical_value_before ?? '________' }}</p>
            <p><span class="font-semibold">Nombre d'heures :</span> {{ $hour_number_before ?? '________' }}</p>
            <p><span class="font-semibold">Prochaine vidange :</span> {{ $next_vidange_before ?? '________' }}</p>
        </div>

        {{-- État après location --}}
        <div>
            <h2 class="text-xl font-semibold mb-4 underline">État Après Location</h2>

            <p class="mb-2"><span class="font-semibold">Technicien :</span> {{ $technician_after_name ?? '____________________________' }}</p>

            <div class="grid grid-cols-2 gap-x-6 gap-y-3 mt-4">
                @foreach(['Propre' => 'is_clean', 'Démarre' => 'is_functional', 'Bien entretenu' => 'is_maintained'] as $label => $key)
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" disabled
                            @if(isset($etat_after) && in_array($key, $etat_after)) checked @endif
                            class="w-4 h-4 border border-gray-400">
                        <span>{{ $label }}</span>
                    </label>
                @endforeach
            </div>

            <p><span class="font-semibold">Grandeur électrique :</span> {{ $electrical_value_after ?? '________' }}</p>
            <p><span class="font-semibold">Grandeur mécanique :</span> {{ $mechanical_value_after ?? '________' }}</p>
            <p><span class="font-semibold">Nombre d'heures :</span> {{ $hour_number_after ?? '________' }}</p>
            <p><span class="font-semibold">Prochaine vidange :</span> {{ $next_vidange_after ?? '________' }}</p>
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