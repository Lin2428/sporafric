<?php

namespace App\Livewire;

use App\Enum\FactureType;
use App\Models\Intervention;
use App\Models\InterventionInfo;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Illuminate\Support\Str;
use Livewire\Component;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

class InterventionActionForm2 extends Component implements HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;

    public Intervention $record;
    public function render()
    {
        return view('livewire.intervention-action-form2');
    }

    public function mount($record)
    {
        $this->record = $record;
    }

    public function editAction(): Action
    {
        dd($this->record->infos);

        return Action::make('edit')
            ->label('Modifier')
            ->modalHeading('Mondifier les informations')
            ->modalWidth('4xl')
            ->form([
                Group::make()
                    ->columns(2)
                    ->schema([
                        Select::make('facturable')
                            ->label("Facturable")
                            ->options(collect(FactureType::cases())
                                ->mapWithKeys(fn($status) => [$status->value => $status->label()])
                                ->toArray())
                            ->reactive(),
                        Select::make('astrinte')
                            ->label("Horaire")
                            ->options(["1" => "Journée normale", "0" => "Astrainte"])
                            ->reactive(),
                        Grid::make(2)
                            ->schema([
                                Section::make('Devis')
                                    ->columns(2)
                                    ->columnSpan(1)
                                    ->schema([
                                        TextInput::make('devis_numero')
                                            ->label('Numéro'),
                                        DatePicker::make('devis_date')
                                            ->label('Date'),
                                        TextInput::make('devis_montant')
                                            ->label('Montant')
                                            ->numeric()
                                            ->columnSpanFull(),
                                        FileUpload::make('devis_fiche')
                                            ->label('Devis PDF')
                                            ->disk('devis')
                                            //->default($this->record->fiche)
                                            ->getUploadedFileNameForStorageUsing(function ($file) {
                                                $record = $this->record;

                                                $customerName = Str::slug($record->contract?->customer?->name ?? 'client');
                                                $date = now()->format('Y-m-d');


                                                return "{$customerName}-{$date}-{$this->record->id}.{$file->getClientOriginalExtension()}";
                                            })->columnSpanFull(),
                                    ]),
                                Section::make('Bon de commande')
                                    ->columns(2)
                                    ->columnSpan(1)
                                    ->schema([
                                        TextInput::make('bc_numero')
                                            ->label('Numéro'),
                                        DatePicker::make('bc_date')
                                            ->label('Date'),

                                        FileUpload::make('bc_fiche')
                                            ->label('Bon de commande PDF')
                                            ->disk('bon_de_commande')
                                            //->default($this->record->fiche)
                                            ->getUploadedFileNameForStorageUsing(function ($file) {
                                                $record = $this->record;

                                                $customerName = Str::slug($record->contract?->customer?->name ?? 'client');
                                                $date = now()->format('Y-m-d');


                                                return "{$customerName}-{$date}-{$this->record->id}.{$file->getClientOriginalExtension()}";
                                            })->columnSpanFull(),
                                    ])
                            ])
                    ])
            ])
            ->action(function (array $data) {
                $this->record->update([
                    'facturable' => $data['facturable'],
                    'astrinte' => $data['astrinte'],
                ]);
                InterventionInfo::updateOrCreate(
                    ['intervention_id' => $this->record->id],
                    [
                        "intervention_id" => $this->record->id,
                        "devis_numero" => $data['devis_numero'],
                        "devis_date" => $data['devis_date'],
                        "devis_fiche" => $data['devis_fiche'],
                        "devis_montant" => $data['devis_montant'],
                        "bc_numero" => $data['bc_numero'],
                        "bc_date" => $data['bc_date'],
                        "bc_fiche" => $data['bc_fiche'],
                    ]
                );
                return redirect(request()->header('Referer'));
            });
    }
}
