<?php
namespace App\Filament\Resources;

use App\Enum\GeneratorStatus;
use App\Filament\Resources\GeneratorResource\Pages;
use App\Models\Generator;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GeneratorResource extends Resource
{
    protected static ?string $model = Generator::class;

    protected static ?string $navigationIcon  = 'icon-generator';
    protected static ?string $navigationGroup = 'Gestion des GE';
    protected static ?string $navigationLabel = 'Groupes Electrogènes';
    protected static ?int $navigationSort     = 0;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->schema([
                        Section::make('Infos générales')
                            ->columns()
                            ->columnSpan(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Marque')
                                    ->columnSpanFull()
                                    ->required(),

                                TextInput::make('modele')
                                    ->label('Modèle')
                                    ->required()
                                    ->columnSpanFull(),

                                TextInput::make('serial_number')
                                    ->label('Numéro de série')
                                    ->columnSpanFull(),

                                FileUpload::make('image')
                                // ->acceptedFileTypes([
                                //     'jpg',
                                //     'png',
                                //     'jpeg',
                                // ])
                                // ->imageCropAspectRatio('1:1')
                                // ->imageResizeTargetWidth('800')
                                // ->imageResizeTargetWidth('800')
                                // ->imageResizeMode('contain')
                                // ->imagePreviewHeight('250')
                                    ->openable()
                                    ->reorderable()
                                    ->label('Image')
                                    ->columnSpanFull(),

                                TextInput::make('power')
                                    ->numeric()
                                    ->label('Puissance (KVA)'),

                                TextInput::make('voltage')
                                    ->numeric()
                                    ->label('Tension (V)'),

                                TextInput::make('frequency')
                                    ->label('Fréquence (Hz)')
                                    ->numeric(),

                                    
                            TextInput::make('fuel_type')
                                ->label('Type de carburant'),
                            ]),
                    ])->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make('Infos techniques')
                            ->schema([
                                DatePicker::make('start-up')
                                    ->label('Mise en service')
                                    ->default(now())
                                    ->extraAttributes(['style' => 'max-width: 230px;']),

                                Select::make('status')
                                    ->options(collect(GeneratorStatus::cases())
                                            ->mapWithKeys(fn($status) => [$status->value => $status->label()])
                                            ->toArray())
                                    ->searchable()
                                    ->label('Statut')
                                    ->preload()
                                    ->extraAttributes(['style' => 'max-width: 230px;'])
                                    ->required(),

                                TextInput::make('houres')
                                    ->label('Heures de fonctionnement')
                                    ->extraAttributes(['style' => 'max-width: 230px;']),

                                DatePicker::make('next_vidange')
                                    ->label('Prochaine vidange')
                                    ->extraAttributes(['style' => 'max-width: 230px;']),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),

            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Image')
                    ->size(50)
                    ->extraAttributes(['style' => 'width: 100px, height: 100px;']),

               TextColumn::make('name')
                    ->label('Marque')
                    ->extraAttributes(['style' => 'font-weight: bold; '])
                    ->searchable(),

                TextColumn::make('modele')
                    ->label('Modèle')
                    ->extraAttributes(['style' => 'font-weight: bold; '])
                    ->searchable(),

                TextColumn::make('status')
                ->label('Statut')
                ->badge()
                ->getStateUsing(function (Generator $record) {
                    return GeneratorStatus::from($record->status)->label();
                })
                ->colors([
                    'success' => 'Disponible',
                    'warning' => 'En maintenance',
                    'info' => 'En location',
                    'danger' => 'Indisponible',
                ]),

                TextColumn::make('serial_number')
                    ->label('Numéro de série')
                    ->searchable(),

                TextColumn::make('power')
                    ->label('Puissance (KVA)')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('voltage')
                    ->label('Tension (V)'),

                TextColumn::make('frequency')
                    ->label('Fréquence (Hz)'),

                TextColumn::make('fuel_type')
                    ->label('Type de carburant')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('houres')
                    ->label('Heures de fonc.')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('next_vidange')
                    ->label('Prochaine vidange')
                    ->date('d/m/Y')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('start-up')
                    ->label('Mise en service')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Ajouté le')
                  ->date('d/m/Y')
                    ->sortable()
                    ->searchable(),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListGenerators::route('/'),
            'create' => Pages\CreateGenerator::route('/create'),
            'edit'   => Pages\EditGenerator::route('/{record}/edit'),
        ];
    }
}
