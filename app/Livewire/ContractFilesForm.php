<?php

namespace App\Livewire;

use App\Models\ContractFiles;
use App\Utils\FunctionUtils;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;

class ContractFilesForm extends Component implements HasForms, HasTable, HasActions
{
    use InteractsWithForms;
    use InteractsWithTable;
    use InteractsWithActions;
    public $contract;

    public function mount($record)
    {
        $this->contract = $record;
    }

    public function table(Table $table): Table
    {
        $model = ContractFiles::where('contract_id', $this->contract->id);
        return $table
            ->heading('Pièces jointes')
            ->query($model)
            ->columns([
                ImageColumn::make('image')
                    ->label('')
                    ->defaultImageUrl(asset('storage/pdf.png'))
                    ->height(50)
                    ->width(50),

                TextColumn::make('file_name')
                    ->label('Fichier')
                    ->getStateUsing(fn(ContractFiles $record) => $record->file_rename ?? $record->file_name)
                    ->disabled()
                    ->columnSpanFull(),

                TextColumn::make('created_at')
                    ->dateTime("d/m/Y à H:i")
                    ->label('Date')
                    ->disabled()
                    ->columnSpanFull(),
            ])
            ->filters([
                //
            ])

            ->recordUrl(fn(ContractFiles $record) => url('storage/' . $record->file_name))
            ->openRecordUrlInNewTab()
            ->actions([
                // ActionGroup::make([

                Action::make('download')
                    ->iconButton()
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn(ContractFiles $record) => FunctionUtils::download($record->file_name, $record->file_rename)),

                Action::make('rename')
                    ->icon('heroicon-o-pencil')
                    ->iconButton()
                    ->form([
                        TextInput::make('file_rename')
                            ->label('Nom du fichier')
                            ->required()
                            ->default(fn(ContractFiles $record) => $record->file_rename ?? $record->file_name)
                            ->maxLength(255),
                    ])
                    ->action(function (ContractFiles $record, array $data) {
                        $record->update($data);
                    }),
                Action::make('delete')
                    ->icon('heroicon-o-trash')
                    ->iconButton()
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (ContractFiles $record) {
                        $record->delete();
                    }),
                // ])
            ])
            ->bulkActions([]);
    }

    public function render()
    {
        return view('livewire.contract-files-form');
    }
}
