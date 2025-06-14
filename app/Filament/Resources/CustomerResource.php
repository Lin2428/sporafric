<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Filament\utils\CustomerUtil;
use App\Models\Customer;
use App\Models\Location\City;
use App\Models\Location\District;
use App\Models\Location\Quartier;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
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

    protected static ?string $navigationGroup = 'Global';
    protected static ?string $navigationLabel = 'Clients';
    protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->schema([
                        TextInput::make('name')
                            ->label('Nom du client')
                            ->required(),

                        Section::make('Contact Commercial')
                            ->columns(2)
                            ->schema([
                                TextInput::make('contact_c_name')
                                    ->label('Nom')
                                    ->columnSpanFull(),

                                TextInput::make('contact_c_email')
                                    ->label('Email')
                                    ->email(),

                                TextInput::make('contact_c_phone')
                                    ->label('Téléphone')
                                    ->tel(),
                            ]),

                        Section::make('Contact Logistique')
                            ->columns(2)
                            ->schema([
                                TextInput::make('contact_l_name')
                                    ->label('Nom')
                                    ->columnSpanFull(),

                                TextInput::make('contact_l_email')
                                    ->label('Email')
                                    ->email(),

                                TextInput::make('contact_l_phone')
                                    ->label('Téléphone')
                                    ->tel(),
                            ]),

                    ])->columnSpan(['lg' => 2]),

                // Group::make()
                //     ->schema([
                //         Repeater::make('customerAdresses')
                //             ->relationship('customerAdresses')
                //             ->label('Adresses')
                //             ->createItemButtonLabel('Ajouter une adresse')
                //             ->columns(1)
                //             ->schema([
                //                 Select::make('country_id')
                //                     ->relationship('country', 'name')
                //                     ->searchable()
                //                     ->preload()
                //                     ->label("Pays")
                //                     ->reactive()
                //                     ->required(),

                //                 Select::make('city_id')
                //                     ->options(function (callable $get) {
                //                         $countryId = $get('country_id');
                //                         return City::where('country_id', $countryId)->pluck('name', 'id');
                //                     })
                //                     ->searchable()
                //                     ->preload()
                //                     ->reactive()
                //                     ->label("Ville")
                //                     ->required(),

                //                 Select::make('district_id')
                //                     ->options(function (callable $get) {
                //                         $cityId = $get('city_id');
                //                         return $cityId ? District::where('city_id', $cityId)->pluck('name', 'id') : [];
                //                     })
                //                     ->searchable()
                //                     ->preload()
                //                     ->reactive()
                //                     ->label("Arrondissement"),

                //                 Select::make('quartier_id')
                //                     ->options(function (callable $get) {
                //                         $districtId = $get('district_id');
                //                         return $districtId ? Quartier::where('district_id', $districtId)->pluck('name', 'id') : [];
                //                     })
                //                     ->searchable()
                //                     ->preload()
                //                     ->reactive()
                //                     ->label("Quartier"),

                //                 TextInput::make('address')
                //                     ->label('Adresse'),

                //                 TextInput::make('postal_code')
                //                     ->label('Code postal'),
                //             ])->columns(1),

                //     ])->columnSpan([
                //         'lg' => 1,
                //     ]),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->defaultPaginationPageOption(50)
            ->columns([

                TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->limit(50),

                TextColumn::make('contact_c_name')
                    ->label('Contact Commercial')
                    ->searchable()
                    ->limit(50),

                TextColumn::make('contact_c_email')
                    ->label('Email Commercial')
                    ->searchable()
                    ->limit(50),

                TextColumn::make('contact_c_phone')
                    ->label('Téléphone Commercial')
                    ->searchable()
                    ->limit(50),

                TextColumn::make('contact_l_name')
                    ->label('Contact Logistique')
                    ->searchable()
                    ->limit(50),

                TextColumn::make('contact_l_email')
                    ->label('Email Logistique')
                    ->searchable()
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
                // Tables\Actions\ActionGroup::make([
                //     Tables\Actions\ViewAction::make(),
                //     Tables\Actions\EditAction::make(),
                //     Tables\Actions\DeleteAction::make()
                // ]),
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
