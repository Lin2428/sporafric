
<div class="relative w-full">
@if ($files)
    <!-- DELETE BUTTON -->
    <button
        x-on:click="
            if(confirm('Supprimer ce document ?')) {
                $wire.deleteFile({{ $files[$index]->id ?? 'null' }})
            }
        "
        class="absolute top-4 right-[11%] text-white px-3 py-1 rounded"
        @if(empty($files)) disabled @endif
    >
        @svg('heroicon-o-trash', ['class' => 'h-4 w-4'])
    </button>

    <!-- PDF Viewer -->
    

    <div class="border rounded overflow-hidden">
        @if(count($files) > 0)
            <iframe 
                src="/storage/{{ $files[$index]['file_name'] }}"
                class="w-full"
                style="height: 70vh;"
            ></iframe>
        @endif
    </div>

    <!-- Controls -->
    <div class="justify-between mt-2">
        <!-- Previous -->
        <button 
            wire:click="previous"
            class="px-4 py-2 bg-gray-200 rounded absolute left-6 top-[45%]"
            @if($index == 0) disabled @endif
        >
            @svg('heroicon-o-chevron-left', ['class' => 'h-5 w-5'])
        </button>

        <!-- Counter -->
        <span class="text-center">{{ $index + 1 }} / {{ count($files) }}</span>

        <!-- Next -->
        <button 
            wire:click="next"
            class="px-4 py-2 bg-gray-200 rounded absolute right-6 top-[45%]"
            @if($index == count($files) - 1) disabled @endif
        >
            @svg('heroicon-o-chevron-right', ['class' => 'h-5 w-5'])
        </button>
    </div>
    @else
        <div class="flex flex-col justify-content-center items-center text-center">
        <div class="mb-4 p-4 border rounded">
            @svg('heroicon-o-document', ['class' => 'h-12 w-12 text-gray-400'])
        </div>
            <p class="text-center text-gray-500">Aucun document disponible.</p>
        </div>
    @endif
</div>