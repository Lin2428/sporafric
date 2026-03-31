<div class="flex justify-end justify-items-end mb-3">
    @livewire('contract-files-modal',['record' => $getRecord()] )
</div>
@livewire('contract-files-form', ['record' => $getRecord()])