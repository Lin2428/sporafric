<?php
namespace App\Filament\Resources\Location;

use App\Filament\Resources\Location\CountryResource\Pages;
use App\Models\Location\Country;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CountryResource extends Resource
{
    protected static ?string $model = Country::class;

    protected static ?string $navigationIcon  = 'heroicon-o-globe-americas';
    protected static ?string $navigationGroup = 'Localisation';
    protected static ?string $navigationLabel = 'Pays';
    protected static ?int $navigationSort     = 0;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nom du pays')
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

                TextColumn::make("created_at")
                    ->label("Création")
                    ->dateTime('d/m/Y'),
                TextColumn::make('updated_at')
                    ->label("Mise à jour")
                    ->dateTime("d/m/Y"),

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
            'index' => Pages\ListCountries::route('/'),
            // 'create' => Pages\CreateCountry::route('/create'),
            // 'edit' => Pages\EditCountry::route('/{record}/edit'),
        ];
    }
}
