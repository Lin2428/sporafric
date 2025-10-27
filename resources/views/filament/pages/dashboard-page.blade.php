<x-filament-panels::page>

    <style>
        .filament-card{
            height:670px;
            overflow: hidden;
            overflow-y: scroll;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 20px;
        }

        .alert-item {
            transition: all 0.3s ease-in-out;
        }

        .alert-item:hover {
            box-shadow: 0 0 5px blue;
            transform: scale(1.05);
        }
    </style>
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
        <div class="col-span-1  shadow-md rounded-lg p-0 filament-card">
            <h2 class="text-lg font-semibold text-red-600 mb-[15px]">⚠️ Alerte</h2>
            <hr class="mt-1">
            <div class="space-y-4 mt-3">
                @foreach($alerts as $alert)
                    <a href="{{ url($alert['url']) }}" target="_blank">
                    <div class="flex items-start gap-3 p-2 mb-2 rounded-lg shadow-sm bg-{{$alert['color']}}-50 border border-{{$alert['color']}}-100 text-{{$alert['color']}}-700 alert-item">
                        <div class="bg-gray-100/50 p-1 rounded-full flex items-center justify-center">
                            {{-- Icone --}}
                            @svg($alert['icon'], 'w-8 h-8 text-' . $alert['color'] . '-600')
                        </div>
                        <div>
                            <div class="font-bold text-sm">{{ $alert['title'] }}</div>
                            <div class="text-sm">
                                {{ $alert['label'] }}
                            </div>
                            <div class="text-xs">
                                 {{$alert['date']? "📅 ".$alert['date']->translatedFormat('l j F Y') : '' }}
                            </div>
                            <div class="text-xs">
                                 {{$alert['hour']? "⏰ ".$alert['hour']."h" : '' }}
                            </div>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 mt-4">
        {{-- Widget stat 2 --}}
        @livewire(\App\Filament\Widgets\InterventionTypeChart::class)
        @if(auth()->user()->hasPermissionTo('widget_RevenuMonsuelChart'))
        @livewire(\App\Filament\Widgets\RevenuMonsuelChart::class)
        @endif
    </div>
</x-filament-panels::page>
