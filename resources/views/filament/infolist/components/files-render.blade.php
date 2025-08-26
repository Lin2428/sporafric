
<div class="flex justify-end justify-items-end mb-3">
    @livewire('generator-files-modal',['record' => $getRecord()] )
</div>
@livewire('generator-files-forms', ['record' => $getRecord()])