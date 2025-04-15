<?php
namespace App\Filament\Resources\Location;

use App\Filament\Resources\Location\QuartierResource\Pages;
use App\Models\Location\Quartier;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class QuartierResource extends Resource
{
    protected static ?string $model = Quartier::class;

    protected static ?string $navigationIcon  = 'heroicon-o-map-pin';
    protected static ?string $navigationGroup = 'Localisation';
    protected static ?string $navigationLabel = 'Quartiers';
    protected static ?int $navigationSort     = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('district_id')
                    ->relationship('district', 'name')
                    ->searchable()
                    ->preload()
                    ->columnSpanFull()
                    ->label('Arrondissement'),

                TextInput::make('name')
                    ->label('Nom du quartier')
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

                TextColumn::make("district.name")
                    ->label("Arrondissement")
                    ->searchable(),

                TextColumn::make("district.city.name")
                    ->label("Ville")
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
            'index'  => Pages\ListQuartiers::route('/'),
            // 'create' => Pages\CreateQuartier::route('/create'),
            // 'edit'   => Pages\EditQuartier::route('/{record}/edit'),
        ];
    }
}
