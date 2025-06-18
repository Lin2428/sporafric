<?php


namespace App\Livewire;

use App\Models\Generator;
use Filament\Actions\Action;
use Faker\Provider\en_US\Text;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Livewire\Component;
use App\Models\Technicien;

class CheckList extends Component implements HasForms, HasInfolists
{
    use InteractsWithForms;
    use InteractsWithInfolists;
    use InteractsWithFormActions;
    
    public $technicien_id = 1;
    public $techniciens;
    public Generator $record;
    public function mount($record)
    {
        $this->record = $record;
        $this->techniciens = Technicien::all()->pluck('name', 'id');   
    }

  


    public function render()
    {
        return view('livewire.check-list', [
            
        ]);
    }
}