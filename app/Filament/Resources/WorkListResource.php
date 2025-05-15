<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkListResource\Pages;
use App\Filament\Resources\WorkListResource\RelationManagers;
use App\Models\WorkList;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WorkListResource extends Resource
{
    protected static ?string $model = WorkList::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Global';
    protected static ?string $navigationLabel = 'Tâches de Maintenance';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make()
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->label('Nom de la tâche')
                            ->columnSpanFull(),
                        Textarea::make('description')
                            ->label('Description de la tâche')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nom de la tâche')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('description')
                ->limit(20)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();

                        if (is_array($state)) {
                            return null;
                        }

                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }
                        return $state;
                    }),
                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->date('d/m/y')
                    ->sortable(),
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
            'index' => Pages\ListWorkLists::route('/'),
            // 'create' => Pages\CreateWorkList::route('/create'),
            // 'edit' => Pages\EditWorkList::route('/{record}/edit'),
        ];
    }
}
