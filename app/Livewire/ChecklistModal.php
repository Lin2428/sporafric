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
                        Section::make('CONTROLE RETOUR LOCATION')
                            ->columns(2)
                            ->schema([
                                Select::make('technicien_id')
                                    ->options($techniciens)
                                    ->label('Technicien(e)')
                                    ->searchable()
                                    ->placeholder('Sélectionner un(e) technicien(e)')
                                    ->required()
                                    ->default($this->record->etat?->technicien_id),

                                TextInput::make('responsable')
                                    ->label('Visa responsable')
                                    ->default($this->record->etat?->responsable),

                                Section::make('Contrôle général')->schema([

                                    CheckboxList::make('checklist_1')
                                        ->label('Contrôle technique')
                                        ->bulkToggleable()
                                        ->options([
                                            'control_1' => 'Contrôle des poignées',
                                            'control_2' => 'contrôle carroserrie',
                                            'control_3' => "Niveau d’huile Moteur",
                                            'control_4' => 'Niveau du Liquide de Refroidissement',
                                            'control_5' => 'Contrôle du filtre à huile',
                                            'control_6' => 'Contrôle du filtre à air',
                                            'control_7' => 'Contrôle du filtre à carburant',
                                            'control_8' => 'Contrôle du circuit carburant',
                                            'control_9' => 'Contrôle du circuit de refroidissement',
                                            'control_10' => 'Contrôle de l’état  des courroies',
                                            'control_11' => 'Contrôle de charge de batterie',
                                        ])
                                        ->afterStateHydrated(function ($component, $record) {
                                            $data = [];

                                            if ($this->record->etat?->control_1 == true) {
                                                $data[] = 'control_1';
                                            }
                                            if ($this->record->etat?->control_2 == true) {
                                                $data[] = 'control_2';
                                            }
                                            if ($this->record->etat?->control_3 == true) {
                                                $data[] = 'control_3';
                                            }
                                            if ($this->record->etat?->control_4 == true) {
                                                $data[] = 'control_4';
                                            }
                                            if ($this->record->etat?->control_5 == true) {
                                                $data[] = 'control_5';
                                            }
                                            if ($this->record->etat?->control_6 == true) {
                                                $data[] = 'control_6';
                                            }
                                            if ($this->record->etat?->control_7 == true) {
                                                $data[] = 'control_7';
                                            }
                                            if ($this->record->etat?->control_8 == true) {
                                                $data[] = 'control_8';
                                            }
                                            if ($this->record->etat?->control_9 == true) {
                                                $data[] = 'control_9';
                                            }
                                            if ($this->record->etat?->control_10 == true) {
                                                $data[] = 'control_10';
                                            }
                                            if ($this->record->etat?->control_11 == true) {
                                                $data[] = 'control_11';
                                            }

                                            $component->state($data);
                                        })
                                        ->columns(2),

                                ]),

                                Section::make('Contrôle en fonctionnement')
                                    ->columns(2)
                                    ->schema([
                                        CheckboxList::make('checklist_2')
                                            ->bulkToggleable()
                                            ->label('')
                                            ->options([
                                                'control_12' => 'Démarrage du GE',
                                                'control_13' => 'Contrôle du circuit de charge moteur',
                                                'control_14' => 'Contrôle ATU',
                                            ])
                                            ->afterStateHydrated(function ($component, $record) {
                                                $data = [];
                                                if ($this->record->etat?->control_12 == true) {
                                                    $data[] = 'control_12';
                                                }
                                                if ($this->record->etat?->control_13 == true) {
                                                    $data[] = 'control_13';
                                                }
                                                if ($this->record->etat?->control_14 == true) {
                                                    $data[] = 'control_14';
                                                }

                                                $component->state($data);
                                            })
                                            ->columns(3)
                                            ->columnSpanFull(),

                                        TextInput::make('control_frequence')
                                        ->numeric()->label('Frequences (Hz)')
                                        ->default($this->record->etat?->control_frequence)
                                        ->columnSpanFull(),

                                        Section::make('Tension de sortie(230V)')
                                            ->columns(3)
                                            ->inlineLabel()
                                            ->schema([
                                                TextInput::make('control_tension.v1')
                                                    ->label('V1n')
                                                    ->default($this->record->etat?->control_tension!=null?$this->record->etat?->control_tension['v1']:""),

                                                TextInput::make('control_tension.v2')
                                                    ->label('V2n')
                                                    ->default($this->record->etat?->control_tension!=null?$this->record->etat?->control_tension['v2']:""),

                                                TextInput::make('control_tension.v3')
                                                    ->label('V3n')
                                                    ->default($this->record->etat?->control_tension!=null?$this->record->etat?->control_tension['v3']:""),
                                            ])
                                            ->columnSpanFull(),

                                        Section::make('Tension de sortie(400V)')
                                            ->columns(3)
                                            ->inlineLabel()
                                            ->schema([
                                                TextInput::make('control_tension_2.u1')
                                                    ->label('U12')
                                                    ->default($this->record->etat?->control_tension_2!=null?$this->record->etat?->control_tension_2['u1']:""),

                                                TextInput::make('control_tension_2.u2')
                                                    ->label('U13')
                                                    ->default($this->record->etat?->control_tension_2!=null?$this->record->etat?->control_tension_2['u2']:""),

                                                TextInput::make('control_tension_2.u3')
                                                    ->label('U23')
                                                    ->default($this->record->etat?->control_tension_2!=null?$this->record->etat?->control_tension_2['u3']:""),
                                            ])
                                            ->columnSpanFull(),
                                        Section::make('Intensité par phase')
                                            ->columns(3)
                                            ->inlineLabel()
                                            ->schema([
                                                TextInput::make('control_intensite.i1')
                                                    ->label('I1')
                                                    ->default($this->record->etat?->control_intensite!=null?$this->record->etat?->control_intensite['i1']:""),

                                                TextInput::make('control_intensite.i2')
                                                    ->label('I2')
                                                    ->default($this->record->etat?->control_intensite!=null?$this->record->etat?->control_intensite['i2']:""),

                                                TextInput::make('control_intensite.i3')
                                                    ->label('I3')
                                                    ->default($this->record->etat?->control_intensite!=null?$this->record->etat?->control_intensite['i3']:""),
                                            ])
                                            ->columnSpanFull(),

                                    ]),

                    
                            ])
                    ])
            ])

            ->action(function (array $data) {

                $data['control_1'] = in_array('control_1', $data['checklist_1']);
                $data['control_2'] = in_array('control_2', $data['checklist_1']);
                $data['control_3'] = in_array('control_3', $data['checklist_1']);
                $data['control_4'] = in_array('control_4', $data['checklist_1']);
                $data['control_5'] = in_array('control_5', $data['checklist_1']);
                $data['control_6'] = in_array('control_6', $data['checklist_1']);
                $data['control_7'] = in_array('control_7', $data['checklist_1']);
                $data['control_8'] = in_array('control_8', $data['checklist_1']);
                $data['control_9'] = in_array('control_9', $data['checklist_1']);
                $data['control_10'] = in_array('control_10', $data['checklist_1']);
                $data['control_11'] = in_array('control_11', $data['checklist_1']);
                $data['control_12'] = in_array('control_12', $data['checklist_2']);
                $data['control_13'] = in_array('control_13', $data['checklist_2']);
                $data['control_14'] = in_array('control_14', $data['checklist_2']);
                
                unset($data['checklist_1']);
                unset($data['checklist_2']);
                unset($data['checklist_3']);

                $data['devis_id'] = $this->record->devisGenerator?->devis_id;
                Checklist::updateOrCreate(
                    [
                        'generator_id' => $this->record->id,
                    ],
                    $data
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
