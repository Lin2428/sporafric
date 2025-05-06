<?php

namespace App\Livewire;

use App\Filament\Utils\WidgetUtils;
use App\Models\Intervention;
use App\Models\InterventionDelivery;
use App\Models\Piece;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Livewire\Component;
use Filament\Forms\Components\TextInput;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

class InterventionActionForm1 extends Component implements HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;

    public Intervention $record;
    public function mount($record)
    {
        $this->record = $record;
    }
    public function getFormSchema(): array
    {
        return [
            TextInput::make('name')->required(),
        ];
    }

    public function openForm(): void
    {
        // logique si besoin
    }

    public function submit(): void
    {
        dd($this->formData); // ou autre traitement
    }

    public function editAction(): Action
    {

        return Action::make('edit')
            ->label('Modifier')
            ->modalHeading('Mondifier les informations')
            ->modalWidth('4xl')
            ->form([
                FileUpload::make('fiche')
                    ->label('Fiche d\'intervention')
                    ->disk('interventions'),
                Repeater::make('pieces')
                    ->label('')
                    ->addActionLabel('Ajouter une pièce')
                    ->schema([
                        WidgetUtils::pieceSelectWidget()
                            ->columnSpan(['lg' => 2])
                            ->required(),
                        TextInput::make('qty')
                            ->label('Quantité')
                            ->numeric()
                            ->minValue(1)
                            ->default(1)
                            ->required(),
                    ])->columns(3)
                    ->grid(2)
            ])
            ->action(function (array $data) {
                Intervention::updateOrCreate(
                    [
                        'id' => $this->record->id
                    ],
                    [
                        'fiche' => $data['fiche'],
                    ]
                );
                $pieces = $data['pieces'];
                $existingIds = collect($pieces)->pluck('id')->filter(); // IDs présents dans le formulaire
                $this->record->pieces()->whereNotIn('id', $existingIds)->delete(); // suppression des anciens

                foreach ($pieces as $piece) {
                    InterventionDelivery::updateOrCreate(
                        [
                            'intervention_id' => $this->record->id,
                            'piece_id' => $piece['piece_id'],
                        ],
                        []
                    );
                }
            });
    }

    public function render()
    {
        return view('livewire.intervention-action-form1');
    }
}
