<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TechnicienResource\Pages;
use App\Filament\Resources\TechnicienResource\RelationManagers;
use App\Models\Technicien;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TechnicienResource extends Resource
{
    protected static ?string $model = Technicien::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $navigationGroup = 'Système';
    protected static ?string $navigationLabel = 'Techniciens';
    protected static ?int $navigationSort = 0;

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
            'index' => Pages\ListTechniciens::route('/'),
            'create' => Pages\CreateTechnicien::route('/create'),
            'edit' => Pages\EditTechnicien::route('/{record}/edit'),
        ];
    }
}
