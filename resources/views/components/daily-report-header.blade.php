@props([
    'title',
    'contracts',
])

<div class="w-full">
        <h1 class="text-2xl font-bold mb-2">{{ $title }}</h1>
    
            <div class="flex items-center gap-4">
                <div class="no-print" id="contract">
                    {{ $this->form }}
                </div>
        
    </div>
</div>
