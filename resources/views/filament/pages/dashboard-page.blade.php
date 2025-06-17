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
            <h2 class="text-lg font-semibold text-red-600 mb-[15px]">⚠️ Interventions en alerte</h2>
            <hr class="mt-1">
            <div class="space-y-4 mt-3">
                @foreach($interventionsDuJour as $intervention)
                    @php
                        $date = \Carbon\Carbon::parse($intervention->date_planifiee);
                        $today = now()->startOfDay();
            
                        if ($date->lt($today)) {
                            $bg = 'red';
                            $icon = 'heroicon-o-x-mark';
                            $title = 'EN RETARD';
                        } elseif ($date->isToday()) {
                            $bg = 'yellow';
                            $icon = 'heroicon-o-exclamation-triangle';
                            $title = 'AUJOURD\'HUI';
                        } elseif ($date->isTomorrow()) {
                            $bg = 'blue';
                            $icon = 'heroicon-o-exclamation-circle';
                            $title = 'DEMAIN';
                        }

                        $url = '/admin/interventions/'.$intervention->id;
                        if($intervention->type == 0) {
                            $url = '/admin/intervention-devis/'.$intervention->id;
                        }
            
                        $label = \App\Enum\InterventionType::from($intervention->type)->label();
                    @endphp
                    <a href="{{ $url }}">
                    <div class="flex items-start gap-3 p-2 mb-2 rounded-lg shadow-sm bg-{{$bg}}-50 border border-{{$bg}}-100 text-{{$bg}}-700">
                        <div class="bg-gray-100/50 p-1 rounded-full flex items-center justify-center">
                            {{-- Icone --}}
                            @svg($icon, 'w-8 h-8 text-{{$bg}}-200')
                        </div>
                        <div>
                            <div class="font-bold text-sm">{{ $title }}</div>
                            <div class="text-sm">
                                {{ $label }}
                            </div>
                            <div class="text-xs">
                                📅 {{ $date->translatedFormat('l j F Y') }}
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
