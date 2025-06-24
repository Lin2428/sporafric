<x-filament-panels::page>
 {{-- Row avec les stats et l'alerte --}}
    <div class="grid grid-cols-3 gap-4">
        {{-- Widget stat 1 --}}
        <div class="col-span-2">
            @livewire(\App\Filament\Widgets\GeneratorLocationStats::class)
            <br>
            <span class="text-3xl font-bold">Maintenance</span>
            <br>
            <br>
             @livewire(\App\Filament\Widgets\InterventionMaintenanceStats::class)
            {{-- <br>
            <br>
            @livewire(\App\Filament\Widgets\InterventionLocationStats::class)
            <br>
            <span class="text-3xl font-bold">Interventions sur les maintenances</span>
            <br>
            <br>
            --}}
        </div>

        {{-- Bloc HTML perso --}}
        <x-filament::card class="col-span-1  shadow-md rounded-lg p-0">
            <h2 class="text-lg font-semibold text-red-600 mb-[15px]">⚠️ Alerte</h2>
            <hr class="mt-1">
            <div class="space-y-4 mt-3">
                @foreach($alerts as $alert)
                    <a href="{{ $alert['url'] }}">
                    <div class="flex items-start gap-3 p-2 mb-2 rounded-lg shadow-sm bg-{{$alert['color']}}-50 border border-{{$alert['color']}}-100 text-{{$alert['color']}}-700">
                        <div class="bg-gray-100/50 p-1 rounded-full flex items-center justify-center">
                            {{-- Icone --}}
                            @svg($alert['icon'], 'w-8 h-8 text-' . $alert['color'] . '-200')
                        </div>
                        <div>
                            <div class="font-bold text-sm">{{ $alert['title'] }}</div>
                            <div class="text-sm">
                                {{ $alert['label'] }}
                            </div>
                            <div class="text-xs">
                                📅 {{ $alert['date']->translatedFormat('l j F Y') }}
                            </div>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </x-filament::card>
    </div>
   
    <div class="grid grid-cols-2 gap-4 mt-4">
        {{-- Widget stat 2 --}}
        @livewire(\App\Filament\Widgets\InterventionTypeChart::class)
        @livewire(\App\Filament\Widgets\RevenuMonsuelChart::class)
    </div>
</x-filament-panels::page>
