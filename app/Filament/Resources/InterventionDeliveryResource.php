<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InterventionDeliveryResource\Pages;
use App\Filament\Resources\InterventionDeliveryResource\RelationManagers;
use App\Models\InterventionDelivery;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InterventionDeliveryResource extends Resource
{
    protected static ?string $model = InterventionDelivery::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box-arrow-down';
    protected static ?string $navigationGroup = 'Maintenance';
    protected static ?string $navigationLabel = 'Livraison / Installation';
    protected static ?int $navigationSort = 1;

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
            'index' => Pages\ListInterventionDeliveries::route('/'),
            'create' => Pages\CreateInterventionDelivery::route('/create'),
            'edit' => Pages\EditInterventionDelivery::route('/{record}/edit'),
        ];
    }
}
