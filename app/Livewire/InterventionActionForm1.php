<?php

namespace App\Livewire;

use App\Filament\Utils\WidgetUtils;
use App\Models\Intervention;
use App\Models\InterventionDelivery;
use App\Models\Piece;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Str;
use Livewire\Component;
use Filament\Forms\Components\TextInput;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Repeater;
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


    public function editAction(): Action
    {
        $pieces = $this->record->pieces->map(function ($piece) {
            return [
                'piece_id' => $piece->pivot->piece_id,
                'qty' => $piece->pivot->qty,
            ];
        })->toArray();

        return Action::make('edit')
            ->label('Modifier')
            ->modalHeading('Mondifier les informations')
            ->modalWidth('4xl')
            ->form([
                FileUpload::make('fiche')
                    ->label('Fiche d\'intervention')
                    ->disk('interventions')
                    ->default($this->record->fiche)
                    ->getUploadedFileNameForStorageUsing(function ($file) {
                        $record = $this->record;

                        $customerName = Str::slug($record->contract?->customer?->name ?? 'client');
                        $date = now()->format('Y-m-d');


                        return "{$customerName}-{$date}-{$this->record->id}.{$file->getClientOriginalExtension()}";
                    }),
                Repeater::make('pieces')
                    ->label('')
                    ->default($pieces)
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
                $this->record->update([
                    'fiche' => $data['fiche'],
                ]);
                $pieces = $data['pieces'];
                $existingIds = collect($pieces)->pluck('piece_id')->filter(); // IDs présents dans le formulaire
                $this->record->pieces()->whereNotIn('piece_id', $existingIds)->delete(); // suppression des anciens

                foreach ($pieces as $piece) {
                    InterventionDelivery::updateOrCreate(
                        [
                            'intervention_id' => $this->record->id,
                            'piece_id' => $piece['piece_id'],
                        ],
                        [
                            'piece_id' => $piece['piece_id'],
                            'qty' => $piece['qty'],
                        ]
                    );
                }

                return redirect(request()->header('Referer'));
            });
    }

    public function render()
    {
        return view('livewire.intervention-action-form1');
    }
}
