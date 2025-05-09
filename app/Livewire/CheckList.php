<?php

namespace App\Livewire;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
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
        return $form
            ->schema([
                Group::make()
                    ->columns(2)
                    ->schema([
                        Section::make('Etat avant location')
                            ->columns(2)
                            ->schema([
                                Select::make('technician_id')
                                    ->options(fn() => Technicien::all()->pluck('name', 'id'))
                                    ->label('Technicien')
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
                                        'is_functional' => 'Fonctionnel',
                                        'is_complete' => 'Complet',
                                        'is_maintained' => 'Bien entretenu',
                                        'is_usable' => 'Utilisable',
                                        'is_acceptable' => 'Acceptable',
                                        'is_safe' => 'Sûr',
                                        'is_legal' => 'Légal',
                                    ]),
                            ])
                            ->columnSpan(['lg' => 1]),

                        Section::make('Etat après location')
                            ->columns(2)
                            ->schema([
                                Select::make('technician_id')
                                    ->options(fn() => Technicien::all()->pluck('name', 'id'))
                                    ->label('Technicien')
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
                                        'is_functional' => 'Fonctionnel',
                                        'is_complete' => 'Complet',
                                        'is_maintained' => 'Bien entretenu',
                                        'is_usable' => 'Utilisable',
                                        'is_acceptable' => 'Acceptable',
                                        'is_safe' => 'Sûr',
                                        'is_legal' => 'Légal',
                                    ]),
                            ])
                            ->columnSpan(['lg' => 1]),
                    ])
            ]);
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
