<?php

namespace App\Filament\Resources;

use App\Enum\GeneratorStatus;
use App\Enum\InterventionStatus;
use App\Enum\InterventionType;
use App\Filament\Resources\GeneratorResource\Pages;
use App\Models\Generator;
use App\Utils\NumberUtils;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GeneratorResource extends Resource
{
    protected static ?string $model = Generator::class;

    protected static ?string $navigationIcon  = 'icon-generator';
    protected static ?string $navigationGroup = 'Maintenance';
    protected static ?string $navigationLabel = 'Groupes Electrogènes';
    protected static ?int $navigationSort     = 2;

    public static function getNavigationBadge(): ?string
    {
        $count = Generator::count();
        return $count;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->schema([
                        Section::make('Infos générales')
                            ->columns()
                            ->columnSpan(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Marque')
                                    ->columnSpanFull()
                                    ->required(),

                                TextInput::make('modele')
                                    ->label('Modèle')
                                    ->required()
                                    ->columnSpanFull(),

                                TextInput::make('serial_number')
                                    ->label('Numéro de série')
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

                Group::make()
                    ->schema([
                        Section::make('Infos techniques')
                            ->schema([
                                DatePicker::make('start-up')
                                    ->label('Mise en service')
                                    ->default(now()),

                                Select::make('status')
                                    ->options(collect(GeneratorStatus::cases())
                                        ->mapWithKeys(fn($status) => [$status->value => $status->label()])
                                        ->toArray())
                                    ->searchable()
                                    ->label('Statut')
                                    ->preload()

                                    ->required(),

                                TextInput::make('houres')
                                    ->label('Heures de fonctionnement'),

                                TextInput::make('next_vidange')
                                    ->label('Prochaine vidange (h)')
                                    ->numeric(),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),

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
                ImageColumn::make('image')
                    ->label('Image')
                    ->size(50)
                    ->extraAttributes(['style' => 'width: 100px, height: 100px;']),

                TextColumn::make('name')
                    ->label('Marque')
                    ->extraAttributes(['style' => 'font-weight: bold; '])
                    ->searchable(),

                TextColumn::make('modele')
                    ->label('Modèle')
                    ->extraAttributes(['style' => 'font-weight: bold; '])
                    ->searchable(),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->getStateUsing(function (Generator $record) {
                        return GeneratorStatus::from($record->status)->label();
                    })
                    ->colors([
                        'success' => 'Disponible',
                        'warning' => 'En maintenance',
                        'info'    => 'En location',
                        'danger'  => 'Indisponible',
                    ]),

                TextColumn::make('serial_number')
                    ->label('Numéro de série')
                    ->searchable(),

                TextColumn::make('power')
                    ->label('Puissance (KVA)')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('voltage')
                    ->label('Tension (V)'),

                TextColumn::make('frequency')
                    ->label('Fréquence (Hz)'),

                TextColumn::make('fuel_type')
                    ->label('Type de carburant')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('houres')
                    ->label('Heures de fonc.')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('next_vidange')
                    ->label('Prochaine vidange')
                    ->date('d/m/Y')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('start-up')
                    ->label('Mise en service')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Ajouté le')
                    ->date('d/m/Y')
                    ->sortable()
                    ->searchable(),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index'  => Pages\ListGenerators::route('/'),
            'create' => Pages\CreateGenerator::route('/create'),
            'edit'   => Pages\EditGenerator::route('/{record}/edit'),
            'view'   => Pages\ViewGenerator::route('/{record}'),
        ];
    }

    public static function buildInfolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Tabs::make('Tabs')
                    ->columnSpanFull()
                    ->tabs([
                        Tabs\Tab::make('Contrat / SItuation')
                            ->icon('heroicon-o-clipboard-document')
                            ->iconPosition(IconPosition::After)
                            ->schema([
                                \Filament\Infolists\Components\Group::make()
                                    ->columnSpan(4)
                                    ->columns(2)
                                    ->schema([
                                        \Filament\Infolists\Components\Section::make('Informations du groupe électrogène')
                                            ->columns(3)
                                            ->schema([
                                                TextEntry::make('status')
                                                    ->label('')
                                                    ->badge()
                                                    ->getStateUsing(function (Generator $record) {
                                                        return GeneratorStatus::from($record->status)->label();
                                                    })
                                                    ->colors([
                                                        'success' => 'Disponible',
                                                        'warning' => ['Pas de GE assigné', 'En maintenance'],
                                                        'info'    => 'En location',
                                                        'danger'  => 'Indisponible',
                                                    ])
                                                    ->columnSpanFull(),

                                                ImageEntry::make('image')
                                                    ->label('')
                                                    ->columnSpanFull()
                                                    ->extraAttributes(['class' => 'w-full d-flex justify-center']),

                                                TextEntry::make('name')
                                                    ->label('GE')
                                                    ->extraAttributes(['class' => 'font-bold text-danger']),

                                                TextEntry::make('modele')
                                                    ->label('Modèle')
                                                    ->extraAttributes(['class' => 'font-bold text-danger']),

                                                TextEntry::make('serial_number')
                                                    ->label('Numéro de série')
                                                    ->extraAttributes(['class' => 'font-bold text-danger']),

                                                TextEntry::make('power')
                                                    ->label('Puissance')
                                                    ->color('success'),

                                                TextEntry::make('fuel_type')
                                                    ->label('Type de carburant')
                                                    ->color('success'),

                                                TextEntry::make('houres')
                                                    ->label('Heures de fonc.')
                                                    ->color('success'),

                                                TextEntry::make('next_vidange')
                                                    ->getStateUsing(fn($record) => NumberUtils::format($record->next_vidange) . ' h')
                                                    ->label('Prochaine vidange')
                                                    ->color('success'),

                                                TextEntry::make('start-up')
                                                    ->label('Mise en service')
                                                    ->date('d/m/Y')
                                                    ->color('success'),

                                                TextEntry::make('created_at')
                                                    ->label('Ajouté le')
                                                    ->date('d/m/Y')
                                                    ->color('success'),
                                            ]),
                                    ]),

                                \Filament\Infolists\Components\Group::make()
                                    ->columnSpan(3)
                                    ->columns(2)
                                    ->schema([
                                        \Filament\Infolists\Components\Section::make('Contrat en cours')
                                            ->columns(2)
                                            ->schema([
                                                TextEntry::make('is_active')
                                                    ->label('')
                                                    ->badge()
                                                    ->getStateUsing(function (Generator $record) {
                                                        return $record->contractGenerator ? $record->contractGenerator->contract->is_active ? 'En cours' : 'Terminé' : 'Pas de contrat';
                                                    })
                                                    ->colors([
                                                        'success' => 'En cours',
                                                        'danger'  => 'Terminé',
                                                    ]),

                                                ImageEntry::make('contractGenerator.contract.customer.logo')
                                                    ->label('')
                                                    ->columnSpanFull()
                                                    ->extraAttributes(['class' => 'w-full d-flex justify-center']),

                                                TextEntry::make('contractGenerator.contract.number')
                                                    ->label('N° contrat')
                                                    ->extraAttributes(['class' => 'font-bold']),

                                                TextEntry::make('contractGenerator.contract.customer.name')
                                                    ->label('Client')
                                                    ->extraAttributes(['class' => 'font-bold']),

                                                TextEntry::make('contractGenerator.contract.forfait')
                                                    ->label('Forfait de maintenance mensuel')
                                                    ->formatStateUsing(fn($state) => NumberUtils::format($state) . ' FCFA')
                                                    ->extraAttributes(['class' => 'font-bold'])
                                                    ->columnSpanFull(),

                                                TextEntry::make('contractGenerator.contract.site')
                                                    ->label('Site')
                                                    ->color('success'),
                                                TextEntry::make('contractGenerator.contract.code_site')
                                                    ->label('Code')
                                                    ->color('success'),
                                                TextEntry::make('contractGenerator.contract.start_date')
                                                    ->date('d/m/Y')
                                                    ->label('A debuter le')
                                                    ->color('success'),
                                                TextEntry::make('contractGenerator.contract.end_date')
                                                    ->date('d/m/Y')
                                                    ->label('Se terminer le')
                                                    ->color('danger'),

                                            ]),
                                    ]),

                                \Filament\Infolists\Components\Group::make()
                                    ->columnSpan(7)
                                    ->columns(2)
                                    ->schema([
                                        \Filament\Infolists\Components\Section::make('Detail du contrat')
                                            ->columns(2)
                                            ->schema([
                                                \Filament\Infolists\Components\Section::make("Localisation")
                                                    ->columns(2)
                                                    ->columnSpan(['lg' => 1])
                                                    ->schema([
                                                        TextEntry::make('contractGenerator.contract.customerAdress.country.name')
                                                            ->label('Pays')
                                                            ->color('success'),
                                                        TextEntry::make('contractGenerator.contract.customerAdress.city.name')
                                                            ->label('Ville')
                                                            ->color('success'),
                                                        TextEntry::make('contractGenerator.contract.customerAdress.district.name')
                                                            ->label('Arrondissement')
                                                            ->color('success'),
                                                        TextEntry::make('contractGenerator.contract.customerAdress.quartier.name')
                                                            ->label('Quartier')
                                                            ->color('success'),
                                                        TextEntry::make('contractGenerator.contract.customerAdress.address')
                                                            ->label('Adresse')
                                                            ->columnSpanFull(),
                                                        TextEntry::make('contractGenerator.contract.customerAdress.postal_code')
                                                            ->label('Code postal')
                                                            ->color('success'),
                                                    ]),

                                                \Filament\Infolists\Components\Section::make('Contact commercial')
                                                    ->columns(2)
                                                    ->columnSpan(['lg' => 1])
                                                    ->schema([
                                                        TextEntry::make('contractGenerator.contract.customerAdress.customer.contact_c_name')
                                                            ->label('Nom')
                                                            ->color('success'),
                                                        TextEntry::make('contractGenerator.contract.customerAdress.customer.contact_c_email')
                                                            ->label('Email')
                                                            ->color('success'),
                                                        TextEntry::make('contractGenerator.contract.customerAdress.customer.contact_c_phone')
                                                            ->label('Téléphone')
                                                            ->color('success'),
                                                    ]),

                                                \Filament\Infolists\Components\Section::make('Contact logistique')
                                                    ->columns(2)
                                                    ->columnSpan(['lg' => 1])
                                                    ->schema([
                                                        TextEntry::make('contractGenerator.contract.customerAdress.customer.contact_l_name')
                                                            ->label('Nom')
                                                            ->color('success'),
                                                        TextEntry::make('contractGenerator.contract.customerAdress.customer.contact_l_email')
                                                            ->label('Email')
                                                            ->color('success'),
                                                        TextEntry::make('contractGenerator.contract.customerAdress.customer.contact_l_phone')
                                                            ->label('Téléphone')
                                                            ->color('success'),
                                                    ]),

                                                \Filament\Infolists\Components\Section::make('Contact sur site')
                                                    ->columns(2)
                                                    ->columnSpan(['lg' => 1])
                                                    ->schema([
                                                        TextEntry::make('contractGenerator.contract.contact_name')
                                                            ->label('Nom')
                                                            ->color('success'),
                                                        TextEntry::make('contractGenerator.contract.contact_email')
                                                            ->label('Email')
                                                            ->color('success'),
                                                        TextEntry::make('contractGenerator.contract.contact_phone')
                                                            ->label('Téléphone')
                                                            ->color('success'),
                                                    ]),

                                                TextEntry::make('vu')
                                                    ->label('Vue sur la carte')
                                                    ->inlineLabel(),

                                                \Filament\Infolists\Components\View::make('filament.infolist.components.map-pointer')
                                                    ->label('')
                                                    ->getStateUsing(function (Generator $record) {
                                                        return [
                                                            'lat' => $record->contractGenerator->contract->lat,
                                                            'lng' => $record->contractGenerator->contract->lng,
                                                        ];
                                                    })
                                                    ->extraAttributes(['class' => 'w-full d-flex justify-center'])
                                                    ->columnSpanFull(),

                                            ]),
                                    ]),
                            ]),
                        Tabs\Tab::make('Interventions')

                            ->icon('heroicon-o-wrench-screwdriver')
                            ->iconPosition(IconPosition::After)
                            ->schema([
                                RepeatableEntry::make("contractGenerator.contract.interventions")
                                    ->label('Interventions de la location en cours')
                                    ->schema([
                                        TextEntry::make('status')
                                            ->label('')
                                            ->badge()
                                            ->getStateUsing(fn($record) => InterventionStatus::from($record->status)->label())
                                            ->colors([
                                                'warning' => "En cours",
                                                'info'  => "Non commencée",
                                                'danger' => "Annulée",
                                                'success' => "Terminée",
                                            ]),
                                        TextEntry::make('created_at')
                                            ->label('Créer le')
                                            ->inlineLabel()
                                            ->date('d/m/y à H:i'),

                                        TextEntry::make('identifiant')
                                            ->label('Numéro de Bon d\'intervention')
                                            ->extraAttributes(['class' => 'font-bold']),

                                        TextEntry::make('type')
                                            ->label('Type')
                                            ->getStateUsing(fn($record) => InterventionType::from($record->type)->label())
                                            ->extraAttributes(['class' => 'font-bold']),

                                        TextEntry::make('description_panne')
                                            ->label('Description de la panne')
                                            ->color('secondary')
                                            ->columnSpanFull(),

                                        \Filament\Infolists\Components\Group::make()
                                            ->columns(4)
                                            ->columnSpanFull()
                                            ->schema([
                                                TextEntry::make('date_prise_appel')
                                                    ->label('Date de prise d’appel')
                                                    ->date('d/m/Y')
                                                    ->color('success'),

                                                TextEntry::make('date_planifiee')
                                                    ->label('Date planifiée')
                                                    ->date('d/m/Y')
                                                    ->color('danger'),

                                                TextEntry::make('start_date')
                                                    ->label('Date début')
                                                    ->date('d/m/Y')
                                                    ->color('success'),

                                                TextEntry::make('end_date')
                                                    ->label('Date limite')
                                                    ->date('d/m/Y')
                                                    ->color('danger'),
                                            ]),

                                        // \Filament\Infolists\Components\View::make('filament.infolist.components.technicien-card')
                                        //         ->label('')
                                        //         ->viewData([
                                        //             'record' => fn ($record) => $record->interventionTechniciens,
                                        //         ])
                                        //         ->columnSpanFull(),

                                        \Filament\Infolists\Components\Group::make()
                                            ->columnSpanFull()
                                            ->schema([
                                                RepeatableEntry::make('interventionTechniciens')
                                                    ->alignCenter()
                                                    ->extraAttributes(['class' => 'border-0 shadow-none p-0 bg-transparent'])
                                                    ->grid(2)
                                                    ->schema([
                                                        Grid::make()
                                                            ->columns(2)
                                                            ->schema([
                                                                ImageEntry::make("photo")
                                                                    ->hiddenLabel()
                                                                    ->inlineLabel()
                                                                    ->circular()
                                                                    ->size(100)
                                                                    ->height(100),
                                                                \Filament\Infolists\Components\Group::make()
                                                                    ->columnSpan(['lg' => 1])
                                                                    ->schema([
                                                                        TextEntry::make('name')
                                                                            ->hiddenLabel()
                                                                            ->extraAttributes(['class' => 'mb-0 gap-y-0 p-0']),
                                                                        TextEntry::make('email')
                                                                            ->hiddenLabel()
                                                                            ->inlineLabel()
                                                                            ->extraAttributes(['class' => 'mb-0 gap-y-0 p-0']),
                                                                        TextEntry::make('phone')
                                                                            ->hiddenLabel()
                                                                            ->inlineLabel()
                                                                            ->extraAttributes(['class' => 'mb-0 gap-y-0 p-0']),
                                                                    ]),
                                                            ]),
                                                    ])
                                                    ->label('Liste des techniciens'),
                                            ]),

                                    ]),
                            ]),
                        Tabs\Tab::make('Pièces de rechange')

                            ->icon('heroicon-o-cog-8-tooth')
                            ->iconPosition(IconPosition::After)
                            ->schema([
                                // ...
                            ]),

                        Tabs\Tab::make('Facturation')
                            ->icon('heroicon-o-ticket')
                            ->iconPosition(IconPosition::After)
                            ->schema([
                                // ...
                            ]),
                    ]),
            ]);
    }
}
