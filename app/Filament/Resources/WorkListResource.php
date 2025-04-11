<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkListResource\Pages;
use App\Filament\Resources\WorkListResource\RelationManagers;
use App\Models\WorkList;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WorkListResource extends Resource
{
    protected static ?string $model = WorkList::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Gestion des GE';
    protected static ?string $navigationLabel = 'Tâches de Maintenance';
    protected static ?int $navigationSort = 2;

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
            'index' => Pages\ListWorkLists::route('/'),
            'create' => Pages\CreateWorkList::route('/create'),
            'edit' => Pages\EditWorkList::route('/{record}/edit'),
        ];
    }
}
