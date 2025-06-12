<?php

namespace App\Livewire;

use Faker\Provider\en_US\Text;
use Filament\Forms\Components\Actions\Action;
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
use Livewire\Component;
use App\Models\Technicien;

class CheckList extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];
    
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
                                    ->required()
                                    ->searchable()
                                    ->placeholder('Sélectionner un(e) technicien(e)')
                                    ->columnSpanFull(),

                                CheckboxList::make('etat')
                                    ->columnSpanFull()
                                    ->columns(2)
                                    ->label('État')
                                    ->required()
                                    ->options([
                                        'is_clean' => 'Propre',
                                        'is_functional' => 'Démarre',
                                        'is_maintained' => 'Bien entretenu',
                                    ]),

                                TextInput::make('electrical_value')
                                    ->label('Grandeur électrique')
                                    ->numeric()
                                    ->required()
                                    ->columnSpanFull(),

                                TextInput::make('mechanical_value')
                                    ->label('Grandeur mécanique')
                                    ->numeric()
                                    ->required()
                                    ->columnSpanFull(),

                                TextInput::make('hour_number')
                                    ->label('Nombre d\'heures')
                                    ->numeric()
                                    ->required()
                                    ->columnSpanFull(),

                                TextInput::make('next_vidange')
                                    ->label('Prochaine vidange')
                                    ->required()
                                    ->columnSpanFull(),
                            ])
                            ->columnSpan(1),

                        Section::make('État après location')
                            ->columns(2)
                            ->schema([
                                Select::make('technician_id_apres')
                                    ->options($techniciens)
                                    ->label('Technicien(e)')
                                    ->required()
                                    ->searchable()
                                    ->placeholder('Sélectionner un(e) technicien(e)')
                                    ->columnSpanFull(),

                                CheckboxList::make('etat_apres')
                                    ->columnSpanFull()
                                    ->columns(2)
                                    ->label('État')
                                    ->required()
                                    ->options([
                                        'is_clean' => 'Propre',
                                        'is_functional' => 'Démarre',
                                        'is_complete' => 'Grandeur électrique',
                                        'is_maintained' => 'Bien entretenu',
                                        'is_usable' => 'Grandeur mécanique',
                                        'is_acceptable' => 'Nb heures',
                                        'is_safe' => 'Prochaine vidange',
                                        'is_legal' => 'Légal',
                                    ]),
                            ])
                            ->columnSpan(1),
                    ]),
            ])
            ->statePath('data')
            ->submitAction(
                Action::make('submit')
                    ->label('Enrégistrer')
                    ->submit('submit') // Appelle la méthode submit()
                    ->color('primary')
            );
    }

    public function create(): void
    {
        dd($this->form->getState());
    }

    public function render()
    {
        return view('livewire.check-list');
    }
}