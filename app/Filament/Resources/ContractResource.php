<?php

namespace App\Filament\Resources;

use App\Enum\GeneratorStatus;
use App\Filament\Resources\ContractResource\Pages;
use App\Filament\Utils\WidgetUtils;
use App\Models\Contract;
use App\Utils\NumberUtils;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\View;
use Filament\Forms\Form;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ContractResource extends Resource
{
    protected static ?string $model = Contract::class;

    protected static ?string $navigationIcon  = 'heroicon-o-clipboard-document';
    protected static ?string $navigationGroup = 'Maintenance';
    protected static ?string $navigationLabel = 'Contrats';
    //protected static ?int $navigationSort     = 0;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->schema([
                        Section::make('Infos générales')
                            ->columns(2)
                            ->schema([
                                WidgetUtils::customerSelectWidget()
                                    ->columnSpanFull(),

                                WidgetUtils::generatorSelectWidget()
                                    ->columnSpanFull(),

                                TextInput::make('number')
                                    ->label('Numéro de contrat')
                                    ->required()
                                    ->columnSpanFull(),

                                TextInput::make('site')
                                    ->label('Site')
                                    ->required()
                                    ->columnSpanFull(),

                                TextInput::make('code_site')
                                    ->label('Code')
                                    ->required()
                                    ->columnSpanFull(),

                                TextInput::make('forfait')
                                    ->label('Forfait de maintenance mensuel')
                                    ->numeric()
                                    ->columnSpanFull(),
                            ]),

                        Section::make('Localisation sur la carte')
                            ->schema([
                                View::make('filament.forms.components.map-picker')
                                    ->label(''),
                            ]),
                    ])->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make('Contact sur place')
                            ->columns(2)
                            ->schema([
                                TextInput::make('contact_name')
                                    ->label('Nom')
                                    ->required()
                                    ->columnSpanFull(),
                                TextInput::make('contact_email')
                                    ->label('Email')
                                    ->required()
                                    ->columnSpanFull(),
                                TextInput::make('contact_phone')
                                    ->label('Téléphone')
                                    ->tel()
                                    ->required()
                                    ->columnSpanFull(),
                            ])->columnSpan(['lg' => 1]),

                        Section::make('Infos contractuelles')
                            ->columns(2)
                            ->schema([
                                DatePicker::make('start_date')
                                    ->label('Date de début')
                                    ->required()
                                    ->columnSpanFull(),

                                DatePicker::make('end_date')
                                    ->label('Date de fin')
                                    ->required()
                                    ->columnSpanFull(),

                                Toggle::make('is_active')
                                    ->label('Statut')
                                    ->onIcon('heroicon-o-check-circle')
                                    ->offIcon('heroicon-o-x-circle')
                                    ->onColor('success')
                                    ->offColor('danger')
                                    ->required(),
                            ])->columnSpan(['lg' => 1]),

                        Section::make('Données de la carte')
                            ->columns(2)
                            ->schema([
                               Textarea::make('adress')
                                    ->label('Adresse')
                                    ->rows(2)
                                    ->required()
                                    ->columnSpanFull(),

                                TextInput::make('lat')
                                    ->label('Latitude')
                                    ->reactive()
                                    ->required()
                                    ->columnSpanFull(),

                                TextInput::make('lng')
                                    ->label('Longitude')
                                    ->required()
                                    ->reactive()
                                    ->columnSpanFull(),
                            ])->columnSpan(['lg' => 1]),
                    ])->columnSpan(['lg' => 1]),

            ])->columns(3);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return static::buildInfolist($infolist);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')
                    ->label('N° contrat')
                    ->searchable()
                    ->sortable()
                    ->extraAttributes(['style' => 'font-weight: bold;'])
                    ->limit(50),

                TextColumn::make('is_active')
                    ->label('Statut')
                    ->badge()
                    ->getStateUsing(fn(Contract $record): string => $record->is_active ? 'En cours' : 'Terminé')
                    ->colors([
                        'success' => 'En cours',
                        'danger' => 'Terminé',
                    ]),

                TextColumn::make('customer.name')
                    ->label('Client')
                    ->searchable()
                    ->sortable()
                    ->extraAttributes(['style' => 'font-weight: bold;'])
                    ->limit(50),

                ImageColumn::make('customer.logo')
                    ->label('Logo')
                    ->circular()
                    ->rounded()
                    ->size(50)
                    ->default('https://ui-avatars.com/api/?name=Logo&background=random'),

                TextColumn::make('site')
                    ->label('Site')
                    ->searchable()
                    ->sortable()
                    ->limit(50),

                TextColumn::make('generator.name')
                    ->label('GE')
                    ->searchable()
                    ->sortable()
                    ->extraAttributes(['style' => 'font-weight: bold;'])
                    ->limit(50),

                TextColumn::make('generator.modele')
                    ->label('Modèle')
                    ->searchable()
                    ->sortable()
                    ->extraAttributes(['style' => 'font-weight: bold;'])
                    ->limit(50),

                ImageColumn::make('generator.image')
                    ->label('Image')
                    ->circular()
                    ->rounded()
                    ->size(50),
            ])
            ->filters([
                SelectFilter::make('is_active'),
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
            'index'  => Pages\ListContracts::route('/'),
            'create' => Pages\CreateContract::route('/create'),
            'edit'   => Pages\EditContract::route('/{record}/edit'),
            'view'   => Pages\ViewContract::route('/{record}'),
        ];
    }

    public static function buildInfolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->columns(7)
            ->schema([
                \Filament\Infolists\Components\Group::make()
                    ->columnSpan(4)
                    ->columns(2)
                    ->schema([
                        \Filament\Infolists\Components\Section::make('Informations du groupe électrogène')
                            ->columns(3)
                            ->schema([
                                TextEntry::make('generator.status')
                                    ->label('')
                                    ->badge()
                                    ->getStateUsing(function (Contract $record) {
                                        return $record->generator ? GeneratorStatus::from($record->generator->status)->label() : "Pas de GE assigné";
                                    })
                                    ->colors([
                                        'success' => 'Disponible',
                                        'warning' => ['Pas de GE assigné', 'En maintenance'],
                                        'info' => 'En location',
                                        'danger' => 'Indisponible',
                                    ])
                                    ->columnSpanFull(),

                                ImageEntry::make('generator.image')
                                    ->label('')
                                    ->columnSpanFull()
                                    ->extraAttributes(['class' => 'w-full d-flex justify-center']),

                                TextEntry::make('generator.name')
                                    ->label('GE')
                                    ->extraAttributes(['class' => 'font-bold text-danger']),

                                TextEntry::make('generator.modele')
                                    ->label('Modèle')
                                    ->extraAttributes(['class' => 'font-bold text-danger']),

                                TextEntry::make('generator.serial_number')
                                    ->label('Numéro de série')
                                    ->extraAttributes(['class' => 'font-bold text-danger']),

                                TextEntry::make('generator.power')
                                    ->label('Puissance')
                                    ->extraAttributes(['class' => 'font-bold text-danger']),

                                TextEntry::make('generator.fuel_type')
                                    ->label('Type de carburant')
                                    ->extraAttributes(['class' => 'font-bold text-danger']),

                                TextEntry::make('generator.houres')
                                    ->label('Heures de fonc.')
                                    ->extraAttributes(['class' => 'font-bold text-danger']),

                                TextEntry::make('generator.next_vidange')
                                    ->date('d/m/Y')
                                    ->label('Prochaine vidange')
                                    ->extraAttributes(['class' => 'font-bold text-danger']),

                                TextEntry::make('generator.start-up')
                                    ->label('Mise en service')
                                    ->date('d/m/Y')
                                    ->extraAttributes(['class' => 'font-bold text-danger']),

                                TextEntry::make('generator.created_at')
                                    ->label('Ajouté le')
                                    ->date('d/m/Y')
                                    ->extraAttributes(['class' => 'font-bold text-danger']),
                            ])
                    ]),

                \Filament\Infolists\Components\Group::make()
                    ->columnSpan(3)
                    ->columns(2)
                    ->schema([
                        \Filament\Infolists\Components\Section::make('Contrat')
                            ->columns(2)
                            ->schema([
                                TextEntry::make('is_active')
                                    ->label('')
                                    ->badge()
                                    ->getStateUsing(function (Contract $record) {
                                        return $record->is_active ? 'En cours' : 'Terminé';
                                    })
                                    ->colors([
                                        'success' => 'En cours',
                                        'danger' => 'Terminé',
                                    ]),


                                ImageEntry::make('customer.logo')
                                    ->label('')
                                    ->columnSpanFull()
                                    ->extraAttributes(['class' => 'w-full d-flex justify-center']),

                                TextEntry::make('number')
                                    ->label('N° contrat')
                                    ->extraAttributes(['class' => 'font-bold']),

                                TextEntry::make('customer.name')
                                    ->label('Client')
                                    ->extraAttributes(['class' => 'font-bold']),

                                TextEntry::make('forfait')
                                    ->label('Forfait de maintenance mensuel')
                                    ->formatStateUsing(fn($state) => NumberUtils::format($state) . ' FCFA')
                                    ->extraAttributes(['class' => 'font-bold'])
                                    ->columnSpanFull(),

                                TextEntry::make('site')
                                    ->label('Site')
                                    ->extraAttributes(['class' => 'font-bold text-danger']),
                                TextEntry::make('code_site')
                                    ->label('Code')
                                    ->extraAttributes(['class' => 'font-bold text-danger']),
                                TextEntry::make('start_date')
                                    ->date('d/m/Y')
                                    ->label('A debuter le')
                                    ->extraAttributes(['class' => 'font-bold text-danger']),
                                TextEntry::make('end_date')
                                    ->date('d/m/Y')
                                    ->label('Se termine le')
                                    ->color('danger'),

                            ])
                    ]),

                \Filament\Infolists\Components\Group::make()
                    ->columnSpan(7)
                    ->columns(2)
                    ->schema([
                        \Filament\Infolists\Components\Section::make('Detail du contrat')
                            ->columns(2)
                            ->collapsible()
                            ->schema([
                           
                                TextEntry::make('adress')
                                            ->label('adresse')
                                            ->columnSpanFull(),

                                \Filament\Infolists\Components\Section::make('Contact commercial')
                                    ->columns(2)
                                    ->columnSpan(['lg' => 1])
                                    ->schema([
                                        TextEntry::make('customer.contact_c_name')
                                            ->label('Nom')
                                            ->lineClamp(2)
                                            ->extraAttributes(['class' => 'font-bold text-danger']),
                                        TextEntry::make('customer.contact_c_email')
                                            ->label('Email')
                                            ->lineClamp(2)
                                            ->extraAttributes(['class' => 'font-bold text-danger']),
                                        TextEntry::make('customer.contact_c_phone')
                                            ->label('Téléphone')
                                            ->extraAttributes(['class' => 'font-bold text-danger']),
                                    ]),

                                \Filament\Infolists\Components\Section::make('Contact logistique')
                                    ->columns(2)
                                    ->columnSpan(['lg' => 1])
                                    ->schema([
                                        TextEntry::make('customer.contact_l_name')
                                            ->label('Nom')
                                            ->lineClamp(2)
                                            ->extraAttributes(['class' => 'font-bold text-danger']),
                                        TextEntry::make('customer.contact_l_email')
                                            ->label('Email')
                                            ->lineClamp(2)
                                            ->extraAttributes(['class' => 'font-bold text-danger']),
                                        TextEntry::make('customer.contact_l_phone')
                                            ->label('Téléphone')
                                            ->extraAttributes(['class' => 'font-bold text-danger']),
                                    ]),

                                \Filament\Infolists\Components\Section::make('Contact sur site')
                                    ->columns(2)
                                    ->columnSpan(['lg' => 1])
                                    ->schema([
                                        TextEntry::make('contact_name')
                                            ->label('Nom')
                                            ->lineClamp(2)
                                            ->extraAttributes(['class' => 'font-bold text-danger']),
                                        TextEntry::make('contact_email')
                                            ->label('Email')
                                            ->lineClamp(2)
                                            ->extraAttributes(['class' => 'font-bold text-danger']),
                                        TextEntry::make('contact_phone')
                                            ->label('Téléphone')
                                            ->extraAttributes(['class' => 'font-bold text-danger']),
                                    ]),

                                TextEntry::make('vu')
                                    ->label('Vue sur la carte')
                                    ->columnSpanFull()
                                    ->inlineLabel(),

                                \Filament\Infolists\Components\View::make('filament.infolist.components.map-pointer')
                                    ->label('')
                                    ->getStateUsing(function (Contract $record) {
                                        return [
                                            'lat' => $record->lat,
                                            'lng' => $record->lng,
                                        ];
                                    })
                                    ->extraAttributes(['class' => 'w-full d-flex justify-center'])
                                    ->columnSpanFull(),

                            ])
                    ]),
            ]);
    }
}
