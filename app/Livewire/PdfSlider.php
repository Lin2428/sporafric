<?php

namespace App\Livewire;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class PdfSlider extends Component implements HasForms, HasActions

{
    use InteractsWithActions;
    use InteractsWithForms;

    public $files;
    public $index = 0;
    public $record;

    public function mount($files = [], $record = null)
    {
        $this->files = $files;
        $this->index = 0;
        $this->record = $record;
    }

    public function next()
    {
        if ($this->index < count($this->files) - 1) {
            $this->index++;
        }
    }

    public function previous()
    {
        if ($this->index > 0) {
            $this->index--;
        }
    }

    public function deleteFile()
    {

        $file = $this->record->files()->find($this->files[$this->index]->id);

        if ($file) {
            Storage::disk('public')->delete($file->file_name);
            $file->delete();

            Notification::make()
                ->title('Document supprimé')
                ->success()
                ->send();

            // Refresh the files list after deletion
            $this->files = $this->record->files()->get();
            $this->index = 0;
        } else {
            Notification::make()
                ->title('Fichier non trouvé')
                ->danger()
                ->send();
        }
    }



    public function render()
    {
        return view('livewire.pdf-slider');
    }
}
