@props([
    'title',
    'contracts',
])

<div class="flex justify-between pb-4">
    <div>
        <h1 class="text-2xl font-bold mb-2">{{ $title }}</h1>
        <div class="flex gap-8">
            <div class="flex items-center gap-4">
                <div class="w-[600px]" id="contract">
                    {{ $this->form }}
                </div>
            </div>

        </div>
    </div>
    <div class="text-sm text-slate-500">
        <p class="text-sm">Générer le : <em
                class="text-slate-600 font-semibold">{{ now()->timezone('Africa/Brazzaville')->format('d/m/Y H:i:s') }}</em></p>
        <p>Imprimé par : <em class="text-slate-600 font-semibold">{{ auth()->user()->name  }}</em></p>
    </div>
</div>
