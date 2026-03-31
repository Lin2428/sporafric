<?php

namespace App\Livewire;

use App\Models\ContractFiles;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Livewire\Component;

class ContractFilesModal extends Component implements HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;

    public $record;

    public function mount($record)
    {
        $this->record = $record->id;
    }



    public function createAction(): Action
    {
        return Action::make('create')
            ->label('Ajouter une pièce jointe')
            ->icon('heroicon-o-plus')
            ->modalHeading('Ajouter une pièce jointe')
            ->modalWidth('4xl')
            ->form([
                // TableRepeater::make('files')
                //     ->emptyLabel('Aucune pièce jointe')
                //     ->label('Pièces jointes')
                //       ->headers([
                //             Header::make('file_name')
                //             ->label('Fichier'),
                //             Header::make('created_at')
                //             ->label('Date'),
                //         ])
                // ->columns(3)
                // ->schema([
                FileUpload::make('file_name')
                    ->label('Fichier')
                    ->directory('contract_files')
                    ->acceptedFileTypes(['application/pdf'])
                    ->storeFileNamesIn('attachment_file_names')
                    ->required(),

                TextInput::make('file_rename')
                    ->label('Nom du fichier')
                    ->placeholder('Renommer le fichier (optionnel)')
                // ])
                // ->defaultItems(0)
                // ->minItems(0)
                // ->columnSpanFull(),
            ])
            ->action(function (array $data) {
                ContractFiles::create([
                    'contract_id' => $this->record,
                    'file_name' => $data['file_name'],
                    'file_rename' => $data['file_rename'],
                    'user_id' => auth()->user()->id,
                ]);

                Notification::make()
                    ->success()
                    ->title('Pièce jointe ajoutée avec succès')
                    ->send();

                return redirect(request()->header('Referer'));
            });
    }
    public function render()
    {
        return view('livewire.contract-files-modal');
    }
}
