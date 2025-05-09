<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Filament\utils\CustomerUtil;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    
    protected static ?string $navigationGroup = 'Location';
    protected static ?string $navigationLabel = 'Clients';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema(CustomerUtil::form())->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo')
                    ->label('Logo')
                    ->circular()
                    ->rounded()
                    ->size(50)
                    ->default('https://ui-avatars.com/api/?name=Logo&background=random'),

                TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable()
                    ->limit(50),

                ToggleColumn::make('is_active')
                    ->label('Actif')
                    ->onIcon('heroicon-o-check-circle')
                    ->offIcon('heroicon-o-x-circle')
                    ->onColor('success')
                    ->offColor('danger'),

                TextColumn::make('contact_c_name')
                    ->label('Contact Commercial')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                TextColumn::make('contact_c_email')
                    ->label('Email Commercial')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                TextColumn::make('contact_c_phone')
                    ->label('Téléphone Commercial')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                TextColumn::make('contact_l_name')
                    ->label('Contact Logistique')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                TextColumn::make('contact_l_email')
                    ->label('Email Logistique')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                TextColumn::make('contact_l_phone')
                    ->label('Téléphone Logistique')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
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
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
