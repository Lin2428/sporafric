<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PieceResource\Pages;
use App\Filament\Resources\PieceResource\RelationManagers;
use App\Models\Piece;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PieceResource extends Resource
{
    protected static ?string $model = Piece::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-8-tooth';
    protected static ?string $navigationGroup = 'Maintenance';
    protected static ?string $navigationLabel = 'Pièces de Réchange';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->schema([
                        Section::make()
                            ->columns()
                            ->columnSpan(2)
                            ->schema([
                                TextInput::make('reference')
                                    ->label('Référence')
                                    ->columnSpanFull()
                                    ->required(),

                                TextInput::make('designation')
                                    ->label('Désignation')
                                    ->required()
                                    ->columnSpanFull(),

                                FileUpload::make('image')
                                // ->acceptedFileTypes([
                                //     'jpg',
                                //     'png',
                                //     'jpeg',
                                // ])
                                // ->imageCropAspectRatio('1:1')
                                // ->imageResizeTargetWidth('800')
                                // ->imageResizeTargetWidth('800')
                                // ->imageResizeMode('contain')
                                // ->imagePreviewHeight('250')
                                    ->openable()
                                    ->reorderable()
                                    ->label('Image')
                                    ->columnSpanFull(),

                                TextInput::make('power')
                                    ->numeric()
                                    ->label('Puissance (KVA)'),

                                TextInput::make('voltage')
                                    ->numeric()
                                    ->label('Tension (V)'),

                                TextInput::make('frequency')
                                    ->label('Fréquence (Hz)')
                                    ->numeric(),

                                    
                            TextInput::make('fuel_type')
                                ->label('Type de carburant'),
                            ]),
                    ])->columnSpan(['lg' => 2]),
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
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                ]),
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
            'index' => Pages\ListPieces::route('/'),
            'create' => Pages\CreatePiece::route('/create'),
            'edit' => Pages\EditPiece::route('/{record}/edit'),
        ];
    }
}
