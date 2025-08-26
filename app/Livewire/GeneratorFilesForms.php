<?php

namespace App\Livewire;

use App\Models\GeneratorFiles;
use App\Utils\FunctionUtils;
use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Faker\Provider\Image;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\FileUpload;
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

class GeneratorFilesForms extends Component implements HasForms, HasTable, HasActions
{
    use InteractsWithForms;
    use InteractsWithTable;
    use InteractsWithActions;
    public $generator;

    public function mount($record)
    {
        $this->generator = $record;
    }

    public function table(Table $table): Table
    {
        $model = GeneratorFiles::where('generator_id', $this->generator->id);
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
                    ->getStateUsing(fn (GeneratorFiles $record) => $record->file_rename ?? $record->file_name)
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
            ->recordUrl(fn (GeneratorFiles $record) => url('storage/' . $record->file_name))
            ->openRecordUrlInNewTab()
            ->actions([
                // ActionGroup::make([

                Action::make('download')
                    ->iconButton()
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn (GeneratorFiles $record) => FunctionUtils::download($record->file_name)),

                Action::make('rename')
                    ->icon('heroicon-o-pencil')
                     ->iconButton()
                    ->form([
                        TextInput::make('file_rename')
                            ->label('Nom du fichier')
                            ->required()
                            ->default(fn (GeneratorFiles $record) => $record->file_rename ?? $record->file_name)
                            ->maxLength(255),
                    ])
                    ->action(function (GeneratorFiles $record, array $data) {
                        $record->update($data);
                    }),
                       Action::make('delete')
                    ->icon('heroicon-o-trash')
                     ->iconButton()
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (GeneratorFiles $record) {
                        $record->delete();
                    }),
                // ])
            ])
            ->bulkActions([
                
            ]);
    }

    public function render()
    {
        return view('livewire.generator-files-forms');
    }
}
