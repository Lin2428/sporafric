<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContractFactureResource\Pages;
use App\Filament\Resources\ContractFactureResource\RelationManagers;
use App\Models\ContractFacture;
use App\Utils\NumberUtils;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ContractFactureResource extends Resource
{
    protected static ?string $model = ContractFacture::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationGroup = 'Ronde';
    protected static ?string $navigationLabel = 'Factures';
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
                TextColumn::make('created_at')
                    ->date('d/m/y')
                    ->label('Créé le')
                    ->sortable(),
                TextColumn::make('intervention.contract.customer.name')
                    ->label('Client')
                    ->sortable(),
                TextColumn::make('intervention.contract.generator')
                    ->getStateUsing(fn($record) => $record->intervention->contract->generator->name . ' ' . $record->intervention->contract->generator->modele . ' ' . $record->intervention->contract->generator->power . 'KVA')
                    ->label('GE')
                    ->sortable(),
                TextColumn::make('montant')
                    ->getStateUsing(fn($record) => NumberUtils::format($record->montant) . ' FCFA')
                    ->label('GE')
                    ->sortable(),
                IconColumn::make('is_paid')
                    ->label('Status')
                    ->boolean()
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
            'index' => Pages\ListContractFactures::route('/'),
            'create' => Pages\CreateContractFacture::route('/create'),
            'edit' => Pages\EditContractFacture::route('/{record}/edit'),
        ];
    }
}
