
<div class="flex justify-end justify-items-end mb-3">
    @livewire('contract-files-modal',['record' => $getRecord()] )
</div>

@livewire('pdf-slider', ['files' => $getState(), 'record' => $getRecord()])

