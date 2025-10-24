@props([
    'alerts' => null,
])

@if($alerts)
@foreach ($alerts as $alert)
<a href="{{ url("/admin/devis/".$alert->devis_id) }}" target="_blank">
    <div
        class="flex items-start gap-3 p-2 mb-2 rounded-lg shadow-sm bg-red-50 border border-red-100 text-red-700 alert-item">
        <div class="bg-gray-100/50 p-1 rounded-full flex items-center justify-center">
            @svg("heroicon-o-exclamation-triangle", 'w-8 h-8 text-red-200')
        </div>
        <div>
            <div class="font-bold text-sm">Rétrait non effectué</div>
            <div class="text-sm">
                Devis: {{ $alert->devis->number }}
            </div>
        </div>
    </div>
</a>
@endforeach
@endif
