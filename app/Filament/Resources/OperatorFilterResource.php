<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OperatorFilterResource\Pages;
use App\Filament\Resources\OperatorFilterResource\RelationManagers;
use App\Models\OperatorFilter;
use ArielMejiaDev\FilamentPrintable\Actions\PrintBulkAction;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OperatorFilterResource extends Resource
{
    protected static ?string $model = OperatorFilter::class;

    protected static ?string $navigationIcon = 'heroicon-o-funnel';
    protected static ?string $navigationGroup = "System";
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationLabel = "Opérateurs de Filtres";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('operator')
                    ->label("Opérateur")
                    ->required(),
                TextInput::make('label')
                    ->label("Libellé")
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultPaginationPageOption(50)
            ->columns([
                Tables\Columns\TextColumn::make('operator')->label("Opérateur"),
                Tables\Columns\TextColumn::make('label')->label("Libellé"),
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
                    PrintBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageOperatorFilters::route('/'),
        ];
    }
}
