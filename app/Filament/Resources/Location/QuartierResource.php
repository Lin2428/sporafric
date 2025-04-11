<?php

namespace App\Filament\Resources\Location;

use App\Filament\Resources\Location\QuartierResource\Pages;
use App\Filament\Resources\Location\QuartierResource\RelationManagers;
use App\Models\Location\Quartier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class QuartierResource extends Resource
{
    protected static ?string $model = Quartier::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    protected static ?string $navigationGroup = 'Localisation';
    protected static ?string $navigationLabel = 'Quartiers';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            'index' => Pages\ListQuartiers::route('/'),
            'create' => Pages\CreateQuartier::route('/create'),
            'edit' => Pages\EditQuartier::route('/{record}/edit'),
        ];
    }
}
