<?php

namespace App\Livewire;

use Filament\Forms\Components\Checkbox;
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
        $techniciens = Technicien::all()->pluck('name', 'id');
        return $form
            ->schema([
                Group::make()
                ->columns(2)
                    ->schema([
                        Section::make('Etat avant location')
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
                                'is_complete' => 'Grandeur électrique',
                                'is_maintained' => 'Bien entretenu',
                                'is_usable' => 'Grandeur mécanique',
                                'is_acceptable' => 'Nb heures',
                                'is_safe' => 'Prochaine vidange',
                                'is_legal' => 'Légal',
                            ]),
                        ])
                        ->columnSpan(1),

                    Section::make('Etat après location')
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
                            'is_complete' => 'Grandeur électrique',
                            'is_maintained' => 'Bien entretenu',
                            'is_usable' => 'Grandeur mécanique',
                            'is_acceptable' => 'Nb heures',
                            'is_safe' => 'Prochaine vidange',
                            'is_legal' => 'Légal',
                        ]),
                    ])
                    ->columnSpan(1),
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