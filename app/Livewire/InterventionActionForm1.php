<?php

namespace App\Livewire;

use App\Filament\Utils\WidgetUtils;
use App\Models\Intervention;
use App\Models\Piece;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Livewire\Component;
use Filament\Forms\Components\TextInput;
use Filament\Actions\Contracts\HasActions;
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
->
            ->form([
                FileUpload::make('fiche')
                    ->label('Fiche d\'intervention')
                    ->disk('interventions'),
            ])
            ->action(function (array $arguments) {
                dd($arguments);
            });
    }

    public function render()
    {
        return view('livewire.intervention-action-form1');
    }
}
