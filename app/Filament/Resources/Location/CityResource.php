<?php

namespace App\Filament\Resources\Location;

use App\Filament\Resources\Location\CityResource\Pages;
use App\Filament\Resources\Location\CityResource\RelationManagers;
use App\Models\Location\City;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CityResource extends Resource
{
    protected static ?string $model = City::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    
    protected static ?string $navigationGroup = 'Localisation';
    protected static ?string $navigationLabel = 'Ville';
    protected static ?int $navigationSort = 1;

    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                        Select::make('country_id')
                            ->relationship('country', 'name')
                            ->searchable()
                            ->preload()
                            ->label('Pays')
                            ->columnSpanFull()
                            ->required(),

                        TextInput::make('name')
                            ->label('Nom de ville')
                            ->columnSpanFull()
                            ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label("Nom de ville")
                    ->searchable(),

                    TextColumn::make("country.name")
                    ->label("Pays")
                    ->searchable(),

                    TextColumn::make("created_at")
                    ->dateTime('d/m/Y')
                    ->sortable(),

                    TextColumn::make('updated_at')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListCities::route('/'),
            // 'create' => Pages\CreateCity::route('/create'),
            // 'edit' => Pages\EditCity::route('/{record}/edit'),
        ];
    }
}
