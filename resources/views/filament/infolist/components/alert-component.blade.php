@props([
    'alerts' => null,
    'title' => null,
    'note' => null,
])
<div class="container">
    <style>
        .container {
            min-height: 335px;
            max-height: 340px;
            overflow-y: auto;
            padding: 10px;
        }

        .alert-item {
            transition: all 0.3s ease-in-out;
        }

        .alert-item:hover {
            box-shadow: 0 0 5px blue;
            transform: scale(1.05);
        }
    </style>
    @if (!$getRecord()->vidange)
        @php
            $color = match (true) {
                $getRecord()->next_vidange <= 5 => 'red',
                $getRecord()->next_vidange <= 20 => 'yellow',
                default => 'green',
            };
        @endphp
        <div
            class="mb-4 p-3 bg-{{ $color }}-50 border border-{{ $color }}-100 text-{{ $color }}-700 rounded-lg">
            <div class="text-sm font-bold mb-2">
                ⚙️ Vidange à effectuer
            </div>
            <div class="text-xs">
                ⏰ {{ $getRecord()->next_vidange }}h
            </div>
        </div>
    @endif
    @if ($alerts)
        @foreach ($alerts as $alert)
            @php
                $url = null;
                if ($alert->contract_id) {
                    $url = '/admin/contracts/' . $alert->contract_id;
                } else {
                    $url = '/admin/devis/' . $alert->devis_id;
                }
            @endphp
            <a href="{{ url($url) }}" target="_blank">
                <div
                    class="flex items-start gap-3 p-2 mb-2 rounded-lg shadow-sm bg-red-50 border border-red-100 text-red-700 alert-item">
                    <div class="bg-gray-100/50 p-1 rounded-full flex items-center justify-center">
                        @svg('heroicon-o-exclamation-triangle', 'w-8 h-8 text-red-600')
                    </div>
                    <div>
                        <div class="font-bold text-sm">{{ $title }}</div>
                        <div class="text-sm">
                            {{ $alert->devis ? 'Devis :' : 'Contrat :' }}
                            {{ $alert->devis?->number ?? $alert->contract?->number }}
                        </div>
                    </div>
                </div>
            </a>
        @endforeach
    @endif
    @if ($note)
        <div class="mt-4 p-3 bg-blue-50 border border-blue-100 text-blue-500 rounded-lg">
            <div class="text-sm">
                {!! $note !!}
            </div>
        </div>
    @endif
</div>
