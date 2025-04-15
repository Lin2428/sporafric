<?php

namespace App\Filament\Resources\Location;

use App\Filament\Resources\Location\DistrictResource\Pages;
use App\Filament\Resources\Location\DistrictResource\RelationManagers;
use App\Models\Location\District;
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

class DistrictResource extends Resource
{
    protected static ?string $model = District::class;

    protected static ?string $navigationIcon = 'heroicon-o-map';
    protected static ?string $navigationGroup = 'Localisation';
    protected static ?string $navigationLabel = 'Arrondissements';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                    Select::make('city_id')
                        ->relationship('city', 'name')
                        ->searchable()
                        ->preload()
                        ->label('Ville')
                        ->columnSpanFull()
                        ->required(),

                    TextInput::make('name')
                        ->label('Nom de l\'arrondissement')
                        ->columnSpanFull()
                        ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label("Nom")
                    ->searchable(),

                    TextColumn::make("city.name")
                    ->label("Ville")
                    ->searchable(),

                    TextColumn::make("city.country.name")
                    ->label("Pays")
                    ->searchable(),

                    TextColumn::make("created_at")
                    ->label("Création")
                    ->dateTime('d/m/Y'),

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
            'index' => Pages\ListDistricts::route('/'),
            // 'create' => Pages\CreateDistrict::route('/create'),
            // 'edit' => Pages\EditDistrict::route('/{record}/edit'),
        ];
    }
}
