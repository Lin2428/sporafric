<?php

namespace App\Filament\Resources;

use App\Enum\SynchronizationParametersType;
use App\Enum\FieldType;
use App\Filament\Resources\SynchronizeParameterResource\Pages\ManageSynchronizeParameters;
use App\Models\SynchronizeParameter;
use ArielMejiaDev\FilamentPrintable\Actions\PrintBulkAction;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SynchronizeParameterResource extends Resource
{
    protected static ?string $model = SynchronizeParameter::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static ?string $navigationGroup = "Paramètres";
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationLabel = "Paramètres de Synchronisation";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('model')
                    ->label("Ressource")
                    ->options(collect(SynchronizationParametersType::cases())
                        ->mapWithKeys(fn($status) => [$status->value => $status->label()])
                        ->toArray())
                    ->searchable()
                    ->preload()
                    ->required()
                    ->columnSpanFull(),
                Group::make([
                    Select::make('field_type')
                        ->options(collect(FieldType::cases())
                            ->mapWithKeys(fn($status) => [$status->value => $status->label()])
                            ->toArray())
                        ->searchable()
                        ->preload()
                        ->label("Type de Champ")
                        ->required()
                        ->placeholder("Sélectionner"),

                    TextInput::make('field')
                        ->label('Champ')
                        ->required(),

                    Select::make('operator_filter_id')
                        ->label("Opérateur de Filtre")
                        ->relationship('operatorFilter', 'operator')
                        ->preload()
                        ->searchable()
                        ->required()
                        ->placeholder("Sélectionner"),

                    TagsInput::make('value')
                        ->label('Valeurs')
                        ->required()
                        ->columnSpan(2),
                ])->columnSpanFull()
                    ->columns(5),


                Toggle::make('is_active')
                    ->label('Actif')
                    ->default(true),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultPaginationPageOption(50)
            ->columns([

                Tables\Columns\TextColumn::make('model')
                    ->label('Ressource')
                    ->formatStateUsing(fn($state) => SynchronizationParametersType::tryFrom($state)?->label() ?? $state),
                Tables\Columns\TextColumn::make('field_type')->label("Type de Champ")
                    ->formatStateUsing(fn($state) => FieldType::tryFrom($state)?->label() ?? $state),
                Tables\Columns\TextColumn::make('field')
                    ->label('Champ'),
                Tables\Columns\TextColumn::make('operatorFilter.operator')
                    ->label("Opérateur de Filtre"),
                Tables\Columns\TextColumn::make('value')
                    ->label('Valeurs')
                    ->formatStateUsing(fn($state) => is_array($state) ? json_encode($state) : $state),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean(),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                    PrintBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageSynchronizeParameters::route('/'),
        ];
    }
}
