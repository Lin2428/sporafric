<?php


namespace App\Livewire;

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

     protected $listeners = ['create','form'];
    public ?array $data = [];

    public $technician_id;
    public $electrical_value;
    public $mechanical_value;
    public $hour_number;
    public $next_vidange;
    public $etat;
    public $technician_id_after;
    public $electrical_value_after;
    public $mechanical_value_after;
    public $hour_number_after;
    public $next_vidange_after;
    public $etat_after;
    
    public function mount(): void
    {
        $this->form->fill();
    }

  

    public function form(Form $form): Form
    {
        $techniciens = Technicien::all()->pluck('name', 'id');

        return $form
            ->schema([
                Group::make()
                    ->columns(2)
                    ->schema([
                        Section::make('État avant location')
                            ->columns(2)
                            ->schema([
                                Select::make('technician_id')
                                    ->options($techniciens)
                                    ->label('Technicien(e)')
                                    ->searchable()
                                    ->placeholder('Sélectionner un(e) technicien(e)')
                                    ->columnSpanFull()
                                    ->afterStateUpdated(function ($state) {
                                        $this->technician_id = $state;
                                        dd($this->technician_id);
                                    })
                                    ->reactive(),

                                CheckboxList::make('etat')
                                    ->columnSpanFull()
                                    ->columns(2)
                                    ->label('État')
                                    ->options([
                                        'is_clean' => 'Propre',
                                        'is_functional' => 'Démarre',
                                        'is_maintained' => 'Bien entretenu',
                                    ])
                                    ->afterStateUpdated(function ($state) {
                                        $this->etat = $state;
                                    }),

                                TextInput::make('electrical_value')
                                    ->label('Grandeur électrique')
                                    ->numeric()
                                    ->columnSpanFull()
                                    ->afterStateUpdated(function ($state) {
                                        $this->electrical_value = $state;
                                    }),

                                TextInput::make('mechanical_value')
                                    ->label('Grandeur mécanique')
                                    ->numeric()
                                    ->columnSpanFull()
                                    ->afterStateUpdated(function ($state) {
                                        $this->mechanical_value = $state;
                                    }),

                                TextInput::make('hour_number')
                                    ->label('Nombre d\'heures')
                                    ->numeric()
                                    ->columnSpanFull()
                                    ->afterStateUpdated(function ($state) {
                                        $this->hour_number = $state;
                                    }),

                                TextInput::make('next_vidange')
                                    ->label('Prochaine vidange')
                                    ->columnSpanFull()
                                    ->afterStateUpdated(function ($state) {
                                        $this->next_vidange = $state;
                                    }),
                            ])
                            ->columnSpan(1),

                        Section::make('État après location')
                            ->columns(2)
                            ->schema([
                                Select::make('technician_id_after')
                                    ->options($techniciens)
                                    ->label('Technicien(e)')
                                    ->searchable()
                                    ->placeholder('Sélectionner un(e) technicien(e)')
                                    ->columnSpanFull(),

                                CheckboxList::make('etat_after')
                                    ->columnSpanFull()
                                    ->columns(2)
                                    ->label('État')
                                    ->options([
                                        'is_clean' => 'Propre',
                                        'is_functional' => 'Démarre',
                                        'is_maintained' => 'Bien entretenu',
                                    ]),

                                TextInput::make('electrical_value_after')
                                    ->label('Grandeur électrique')
                                    ->numeric()
                                    ->columnSpanFull(),

                                TextInput::make('mechanical_value_after')
                                    ->label('Grandeur mécanique')
                                    ->numeric()
                                    ->columnSpanFull(),

                                TextInput::make('hour_number_after')
                                    ->label('Nombre d\'heures')
                                    ->numeric()
                                    ->columnSpanFull(),

                                TextInput::make('next_vidange_after')
                                    ->label('Prochaine vidange')
                                    ->columnSpanFull(),
                            ])
                            ->columnSpan(1),
                    ]),
            ]);
    }


    public function create()
    {
        dd($this->form->getState());
    }

    public function render()
    {
        return view('livewire.check-list', [
            
        ]);
    }
}