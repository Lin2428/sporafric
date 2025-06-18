<?php
namespace App\Livewire;

use App\Models\Checklist;
use App\Models\Generator;
use App\Models\Technicien;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Livewire\Component;

class ChecklistModal extends Component implements HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;
    public Generator $record;
    public function mount($record)
    {
        $this->record = $record;
    }

    public function editAction(): Action
    {
        $techniciens = Technicien::all()->pluck('name', 'id');
        return Action::make('edit')
            ->label('Modifier')
            ->modalHeading('Mondifier les informations')
            ->modalWidth('4xl')
            ->form([
                Group::make()
                    ->columns(2)
                    ->schema([
                        Section::make('État avant location')
                            ->columns(2)
                            ->schema([
                                Select::make('technicien_id')
                                    ->options($techniciens)
                                    ->label('Technicien(e)')
                                    ->searchable()
                                    ->placeholder('Sélectionner un(e) technicien(e)')
                                    ->columnSpanFull()
                                    ->required()
                                    ->default($this->record->etat?->technicien_id),

                                CheckboxList::make('etat')
                                    ->columnSpanFull()
                                    ->columns(2)
                                    ->label('État')
                                    ->options([
                                        'is_clean'      => 'Propre',
                                        'is_functional' => 'Démarre',
                                        'is_maintained' => 'Bien entretenu',
                                    ])->default([
                                        $this->record->etat?->is_clean,
                                        $this->record->etat?->is_functional,
                                        $this->record->etat?->is_maintained,
                                    ]),

                                TextInput::make('electrical_value')
                                    ->label('Grandeur électrique')
                                    ->numeric()
                                    ->columnSpanFull()
                                    ->default($this->record->etat?->electrical_value),

                                TextInput::make('mechanical_value')
                                    ->label('Grandeur mécanique')
                                    ->numeric()
                                    ->columnSpanFull()
                                    ->default($this->record->etat?->mechanical_value),

                                TextInput::make('hour_number')
                                    ->label('Nombre d\'heures')
                                    ->numeric()
                                    ->columnSpanFull()
                                    ->default($this->record->etat?->hour_number),

                                TextInput::make('next_vidange')
                                    ->label('Prochaine vidange')
                                    ->columnSpanFull()
                                    ->default($this->record->etat?->next_vidange),
                            ])
                            ->columnSpan(1),

                        Section::make('État après location')
                            ->columns(2)
                            ->schema([
                                Select::make('technicien_id_after')
                                    ->options($techniciens)
                                    ->label('Technicien(e)')
                                    ->searchable()
                                    ->placeholder('Sélectionner un(e) technicien(e)')
                                    ->columnSpanFull()
                                    ->default($this->record->etat?->technicien_id_after),

                                CheckboxList::make('etat_after')
                                    ->columnSpanFull()
                                    ->columns(2)
                                    ->label('État')
                                    ->options([
                                        'is_clean_after'      => 'Propre',
                                        'is_functional_after' => 'Démarre',
                                        'is_maintained_after' => 'Bien entretenu',
                                    ])->default([
                                        $this->record->etat?->is_clean_after,
                                        $this->record->etat?->is_functional_after,
                                        $this->record->etat?->is_maintained_after,
                                    ]),

                                TextInput::make('electrical_value_after')
                                    ->label('Grandeur électrique')
                                    ->numeric()
                                    ->columnSpanFull()
                                    ->default($this->record->etat?->electrical_value_after),

                                TextInput::make('mechanical_value_after')
                                    ->label('Grandeur mécanique')
                                    ->numeric()
                                    ->columnSpanFull()
                                    ->default($this->record->etat?->mechanical_value_after),

                                TextInput::make('hour_number_after')
                                    ->label('Nombre d\'heures')
                                    ->numeric()
                                    ->columnSpanFull()
                                    ->default($this->record->etat?->hour_number_after),

                                TextInput::make('next_vidange_after')
                                    ->label('Prochaine vidange')
                                    ->columnSpanFull()
                                    ->default($this->record->etat?->next_vidange_after),
                            ])
                            ->columnSpan(1),
                    ]),
            ])
            ->action(function (array $data) {
                dd($data);
                $etat = array_filter($data['etat']);
                $etat_after = array_filter($data['etat_after']);
                    Checklist::updateOrCreate(
                        [
                            'generator_id' => $this->record->id,
                        ],
                        [
                            'technicien_id' => $data['technicien_id'],
                            'electrical_value' => $data['electrical_value'],
                            'mechanical_value' => $data['mechanical_value'],
                            'hour_number' => $data['hour_number'],
                            'next_vidange' => $data['next_vidange'],
                            'technicien_id_after' => $data['technicien_id_after'],
                            'is_clean' =>in_array('is_clean', $etat) ? 'is_clean' : null,
                            'is_functional' => in_array('is_functional', $etat) ? 'is_functional' : null,
                            'is_maintained' => in_array('is_maintained', $etat) ? 'is_maintained' : null,
                            'etat_after' => $data['etat_after'],
                            'electrical_value_after' => $data['electrical_value_after'],
                            'mechanical_value_after' => $data['mechanical_value_after'],
                            'hour_number_after' => $data['hour_number_after'],
                            'next_vidange_after' => $data['next_vidange_after'],
                            'is_clean_after' => in_array('is_clean_after', $etat_after) ? 'is_clean_after' : null,
                            'is_functional_after' => in_array('is_functional_after', $etat_after) ? 'is_functional_after' : null,
                            'is_maintained_after' => in_array('is_maintained_after', $etat_after) ? 'is_maintained_after' : null,
                        ]
                    );

                Notification::make()
                    ->success()
                    ->title('Checklist modifiée')
                    ->send();

                return redirect(request()->header('Referer'));
            });
    }

    public function render()
    {
        return view('livewire.checklist-modal');
    }
}
