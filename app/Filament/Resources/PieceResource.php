<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PieceResource\Pages;
use App\Filament\Resources\PieceResource\RelationManagers;
use App\Models\Piece;
use App\Utils\NumberUtils;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

// class PieceResource extends Resource
// {
//     protected static ?string $model = Piece::class;

//     protected static ?string $navigationIcon = 'heroicon-o-cog-8-tooth';
//     protected static ?string $navigationGroup = 'Global';
//     protected static ?string $navigationLabel = 'Pièces de Réchange';
//     protected static ?int $navigationSort = 2;

//     public static function form(Form $form): Form
//     {
//         return $form
//             ->schema([
//                 Group::make()
//                     ->schema([
//                         Section::make()
//                             ->columns()
//                             ->columnSpan(2)
//                             ->schema([
//                                 TextInput::make('reference')
//                                     ->label('Référence')
//                                     ->columnSpanFull()
//                                     ->required(),

//                                 TextInput::make('designation')
//                                     ->label('Désignation')
//                                     ->required()
//                                     ->columnSpanFull(),

//                                 FileUpload::make('image')
//                                     // ->acceptedFileTypes([
//                                     //     'jpg',
//                                     //     'png',
//                                     //     'jpeg',
//                                     // ])
//                                     // ->imageCropAspectRatio('1:1')
//                                     // ->imageResizeTargetWidth('800')
//                                     // ->imageResizeTargetWidth('800')
//                                     // ->imageResizeMode('contain')
//                                     // ->imagePreviewHeight('250')
//                                     ->openable()
//                                     ->reorderable()
//                                     ->label('Image')
//                                     ->columnSpanFull(),

//                                 TextInput::make('duree_vie')
//                                     ->numeric()
//                                     ->label('Durée de vie'),

//                                 TextInput::make('pr')
//                                     ->numeric()
//                                     ->label('Prix d\'achat'),

//                                 TextInput::make('pv')
//                                     ->label('Prix de vente')
//                                     ->numeric(),


//                             ]),
//                     ])->columnSpan(['lg' => 2]),
//             ]);
//     }

//     public static function table(Table $table): Table
//     {
//         return $table
//             ->columns([
//                 ImageColumn::make('image')
//                     ->label('Image')
//                     ->size(50)
//                     ->extraAttributes(['style' => 'width: 100px, height: 100px;']),

//                 TextColumn::make('reference')
//                     ->label('Reference')
//                     ->searchable(),

//                 TextColumn::make('designation')
//                     ->label('Designation')
//                     ->extraAttributes(['style' => 'font-weight: bold; '])
//                     ->searchable(),

//                 TextColumn::make('pr')
//                     ->label('Prix d\'achat')
//                     ->getStateUsing(fn($record) => NumberUtils::format($record->pr) . " FCFA")
//                     ->searchable(),

//                 TextColumn::make('pv')
//                     ->label('Prix de vente')
//                     ->getStateUsing(fn($record) => NumberUtils::format($record->pr) . " FCFA")
//                     ->searchable(),

//                 TextColumn::make('duree_vie')
//                     ->label('Durée de vie')
//                     ->getStateUsing(fn($record) => NumberUtils::format($record->duree_vie) . "h"),
//             ])
//             ->filters([
//                 //
//             ])
//             ->actions([
//                 Tables\Actions\ActionGroup::make([
//                     Tables\Actions\ViewAction::make(),
//                     Tables\Actions\EditAction::make(),
//                     Tables\Actions\DeleteAction::make()
//                 ]),
//             ])
//             ->bulkActions([
//                 Tables\Actions\BulkActionGroup::make([
//                     Tables\Actions\DeleteBulkAction::make(),
//                 ]),
//             ]);
//     }

//     public static function getRelations(): array
//     {
//         return [
//             //
//         ];
//     }

//     public static function getPages(): array
//     {
//         return [
//             'index' => Pages\ListPieces::route('/'),
//             'create' => Pages\CreatePiece::route('/create'),
//             'edit' => Pages\EditPiece::route('/{record}/edit'),
//         ];
//     }
// }
