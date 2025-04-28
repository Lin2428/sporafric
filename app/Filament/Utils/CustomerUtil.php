<?php

namespace App\Filament\utils;

use App\Models\Location\City;
use App\Models\Location\District;
use App\Models\Location\Quartier;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;

class CustomerUtil
{
    public static function form(): array
    {
        return [
            Group::make()
                ->schema([
                    TextInput::make('name')
                        ->label('Nom du client')
                        ->required(),

                    FileUpload::make('logo')
                        ->label('Logo'),

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

            Group::make()
                ->schema([
                    Repeater::make('customerAdresses')
                        ->relationship('customerAdresses')
                        ->label('Adresses')
                        ->createItemButtonLabel('Ajouter une adresse')
                        ->columns(1)
                        ->schema([
                            Select::make('country_id')
                                ->relationship('country', 'name')
                                ->searchable()
                                ->preload()
                                ->label("Pays")
                                ->reactive()
                                ->required(),

                            Select::make('city_id')
                                ->options(function (callable $get) {
                                    $countryId = $get('country_id');
                                    return City::where('country_id', $countryId)->pluck('name', 'id');
                                })
                                ->searchable()
                                ->preload()
                                ->reactive()
                                ->label("Ville")
                                ->required(),

                            Select::make('district_id')
                                ->options(function (callable $get) {
                                    $cityId = $get('city_id');
                                    return $cityId ? District::where('city_id', $cityId)->pluck('name', 'id') : [];
                                })
                                ->searchable()
                                ->preload()
                                ->reactive()
                                ->label("Arrondissement"),

                            Select::make('quartier_id')
                                ->options(function (callable $get) {
                                    $districtId = $get('district_id');
                                    return $districtId ? Quartier::where('district_id', $districtId)->pluck('name', 'id') : [];
                                })
                                ->searchable()
                                ->preload()
                                ->reactive()
                                ->label("Quartier"),

                            TextInput::make('address')
                                ->label('Adresse'),

                            TextInput::make('postal_code')
                                ->label('Code postal'),
                        ])->columns(1),

                ])->columnSpan([
                    'lg' => 1,
                ]),

        ];
    }

    public static function filter(): array
    {
        return [
            Filter::make('created')
                ->form([
                    DatePicker::make('created')
                        ->label('Date de création'),
                ]),

            // SelectFilter::make('user_id')
            //     ->relationship('user', 'name')
            //     ->label('Attribué à')
            //     ->searchable()
            //     ->preload(),

            SelectFilter::make('country_id')
                ->relationship('country', 'name')
                ->label('Pays')
                ->preload()
                ->searchable(),

            SelectFilter::make('city_id')
                ->relationship('city', 'name')
                ->label('Ville')
                ->preload()
                ->searchable(),

        ];
    }
}
