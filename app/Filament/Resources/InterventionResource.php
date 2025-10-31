<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use App\Enum\InterventionStatus;
use App\Enum\InterventionType;
use App\Enum\InterventionTypeService;
use App\Filament\Resources\GeneratorResource\Pages\ViewIntervention;
use App\Filament\Resources\InterventionResource\Pages;
use App\Filament\Utils\InterventionUtil;
use App\Filament\Utils\WidgetUtils;
use App\Models\Generator;
use App\Models\Intervention;
use App\Models\Piece;
use App\Utils\NumberUtils;
use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InterventionResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Intervention::class;

    protected static ?string $label           = "Interventions Maintenance";
    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationGroup = 'Maintenance';
    protected static ?string $navigationLabel = 'Interventions';
    protected static ?int $navigationSort = 0;
    public static function getNavigationBadge(): ?string
    {
        $count = Intervention::where('type_service', InterventionTypeService::MAINTENANCE->value)->count();
        return $count;
    }

    public static function form(Form $form): Form
    {

        $onUpdate = function (Set $set, Get $get) {
            $pieces = $get('../../pieces');

            if ($pieces) {
                foreach ($pieces as $piece) {
                    $price = Piece::find($piece['piece_id'])?->pv;

                    $set('price', $price ?? 0);
                }
            }
        };

        return $form
            ->schema([
                Group::make()
                    ->schema([
                        Section::make('Informations sur l’intervention')
                            ->columns(2)
                            ->schema([
                                Select::make('type_service')
                                    ->label("Location ou Maintenance ?")
                                    ->options(collect(InterventionTypeService::cases())
                                        ->mapWithKeys(fn($status) => [$status->value => $status->label()])
                                        ->toArray())
                                    ->default("1")
                                    ->disabled()
                                    ->columnSpanFull(),

                                TextInput::make('numero')
                                    ->label('Numéro')
                                    ->default(NumberUtils::intevention_numero('INT-MAINT'))
                                    ->disabled()
                                    ->columnSpanFull(),

                                Select::make('type_activite')
                                    ->label("Type d'activité")
                                    ->options([1 => "Sous contrat", 0 => "Hors contrat"])
                                    ->columnSpanFull()
                                    ->required()
                                    ->reactive(),

                                WidgetUtils::contractSelectWidget()
                                    ->columnSpanFull()
                                    ->reactive()
                                    ->required()
                                    ->visible(fn(callable $get) => $get('type_activite') == "1"),

                                WidgetUtils::generatorSelectWidget(type: null, isDispo: false, onUpdate: function (Set $set, $state) {
                                    $generator = Generator::find($state);

                                    $set('houres', $generator?->houres);
                                    $set('next_vidange', $generator?->next_vidange);
                                    $set('prochain_visite', $generator?->prochain_visite);
                                })
                                    ->columnSpanFull()
                                    ->required()
                                    ->reactive()
                                    ->visible(fn(callable $get) => $get('contract_id') != null && $get('type_activite') == "1"),

                                Section::make('Information sur le client')
                                    ->columns(2)
                                    ->schema([
                                        WidgetUtils::customerSelectWidget()
                                            ->required()
                                            ->columnSpanFull(),

                                        WidgetUtils::generatorSelectWidget(type: 2, isDispo: false, onUpdate: function (Set $set, $state) {
                                            $generator = Generator::find($state);

                                            $set('houres', $generator?->houres);
                                            $set('next_vidange', $generator?->next_vidange);
                                            $set('prochain_visite', $generator?->prochain_visite);
                                        })
                                            ->columnSpanFull()
                                            ->required()
                                            ->reactive(),
                                        TextInput::make('site')
                                            ->label("Site")->columnSpanFull(),
                                    ])->visible(fn(callable $get) => $get('type_activite') == "0"),


                                DateTimePicker::make('date_prise_appel')
                                    ->label('Date de prise d’appel')
                                    ->default(now())
                                    ->required(),

                                DateTimePicker::make('date_planifiee')
                                    ->label('Date planifiée'),

                                TextInput::make('identifiant')
                                    ->unique(ignoreRecord: true)
                                    ->label('Numéro de Bon de travaux'),

                                Select::make('type')
                                    ->label('Type')
                                    ->options(collect(InterventionType::cases())
                                        ->mapWithKeys(fn($status) => [$status->value => $status->label()])
                                        ->toArray())
                                    ->reactive(),

                                TextInput::make('houres')
                                    ->numeric()
                                    ->label('H de fonctionnement du GE')
                                    ->formatStateUsing(function (Get $get) {
                                        $generator = Generator::find($get('generator_id'));

                                        return $generator?->houres;
                                    })
                                    ->reactive(),

                                //    TextInput::make('next_vidange')
                                //         ->numeric()
                                //         ->reactive()
                                //         ->disabled()
                                //         ->dehydrated(true)
                                //         ->label('Temps restant avant vidange')
                                //         ->formatStateUsing(function (Get $get) {
                                //             $generator = Generator::find($get('generator_id'));

                                //             return $generator?->next_vidange;
                                //         })->visible(fn(callable $get) => $get('type_activite')),

                                TextInput::make('prochain_visite')
                                    ->numeric()
                                    ->reactive()
                                    ->label('Vidange programmée')
                                    ->formatStateUsing(function (Get $get) {
                                        $generator = Generator::find($get('generator_id'));

                                        return $generator?->prochain_visite;
                                    }),

                                WidgetUtils::generatorSelectWidget(name: "new_generator_id", isgetAll: true, type: null, isDispo: false)
                                    ->label("GE remplacé")
                                    ->columnSpanFull()
                                    ->reactive()
                                    ->required()
                                    ->visible(fn(callable $get) => $get('type') == InterventionType::REMPLACEMENT->value),

                                RichEditor::make('description_panne')
                                    ->label('Constat')
                                    ->columnSpanFull(),

                                RichEditor::make('travaux')
                                    ->label('Travaux effectués')
                                    ->columnSpanFull(),
                            ])
                    ])->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make('Infos internes')
                            ->columns(1)
                            ->schema([
                                DateTimePicker::make('start_date')
                                    ->label('Date de début')
                                    ->reactive(),

                                DateTimePicker::make('end_date')
                                    ->label('Date de fin'),

                                Select::make('status')
                                    ->label('Statut')
                                    ->options(collect(InterventionStatus::cases())
                                        ->mapWithKeys(fn($status) => [$status->value => $status->label()])
                                        ->toArray())
                                    ->required(),

                                Select::make('interventionTechniciens.technicien_id')
                                    ->relationship('interventionTechniciens', 'name')
                                    ->label('Techniciens assignés')
                                    ->multiple()
                                    ->preload()
                                    ->searchable()
                                    ->placeholder('Sélectionner un technicien'),

                                TextInput::make('numero_devis')
                                    ->label('Numéro du devis')
                            ]),
                        Section::make('Autre information')
                            ->columns(2)
                            ->schema([
                                TextInput::make('montant')
                                    ->label('Montant de la main d\'oeuvre')
                                    ->numeric()
                                    ->columnSpanFull(),

                                Repeater::make('fiches')
                                    ->label('')
                                    ->relationship('fiches')
                                    ->addActionLabel('Ajouter une pièce jointe')
                                    ->schema([
                                        FileUpload::make('fiche')
                                            ->hiddenLabel()
                                            ->disk('devis')
                                            ->downloadable()
                                            ->openable()
                                            ->columnSpanFull()
                                            ->storeFileNamesIn('attachment_file_names'),

                                    ])->columnSpanFull(),
                            ]),
                    ])->columnSpan(['lg' => 1]),


                // Section::make('Pièces livrées')
                //     ->columns(2)
                // ->schema([
                TableRepeater::make('pieces')
                    ->label('Pièces livrées')
                    ->emptyLabel('Aucune pièce livrée')
                    ->headers([
                        Header::make('piece_id')
                            ->label('Pièce'),
                        Header::make('qty')
                            ->label('Quantité'),
                        Header::make('price')
                            ->label('Prix'),
                    ])
                    ->formatStateUsing(function ($record) {
                        if (empty($record->pieces)) return [];

                        return $record->pieces?->map(function ($piece) {
                            return [
                                'piece_id' => $piece->id,
                                'qty' => $piece->pivot->qty,
                                'price' => $piece->pivot->price,
                            ];
                        })->toArray();
                    })
                    ->addActionLabel('Ajouter une pièce')
                    ->schema([
                        WidgetUtils::pieceSelectWidget($onUpdate)
                            ->reactive()
                            ->required(),
                        TextInput::make('qty')
                            ->label('Quantité')
                            ->numeric()
                            ->minValue(1)
                            ->default(1)
                            ->required(),
                        TextInput::make('price')
                            ->label('Prix unitaire')
                            ->numeric()
                            ->minValue(0)
                            ->reactive()
                            ->required(),
                    ])->columnSpanFull()
                    ->columns(3)
                // ])->columnSpanFull(),

            ])->columns(3);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return static::buildInfolist($infolist);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(static::getEloquentQuery()->where('type_service', InterventionTypeService::MAINTENANCE->value))
            ->defaultSort('date_planifiee', 'desc')
            ->defaultPaginationPageOption(50)
            ->columns(InterventionUtil::table())
            ->filters([
                Filter::make('status')
                    ->form([
                        CheckboxList::make('type_activite')
                            ->label("Type d'activité")
                            ->options([
                                '1' => "Sous contrat",
                                '0' => "Hors contrat"
                            ])
                            ->reactive(),
                        CheckboxList::make('status')
                            ->options(collect(InterventionStatus::cases())
                                ->mapWithKeys(fn($status) => [$status->value => $status->label()])
                                ->toArray()),

                        Checkbox::make('later')
                            ->label('En retard')
                            ->reactive(),

                        DatePicker::make('date_planifiee')
                            ->label('Date planifiée'),

                        DatePicker::make('date_prise_appel')
                            ->label('Date de prise d\'appel'),

                        Select::make('type')
                            ->label('Type')
                            ->options(collect(InterventionType::cases())
                                ->mapWithKeys(fn($status) => [$status->value => $status->label()])
                                ->toArray()),
                    ])
                    ->query(
                        fn(Builder $query, array $data) => $query
                            ->when($data['type_activite'] ?? null, fn(Builder $query, array $type) => $query->where('type_activite',  $type))
                            ->when($data['status'] ?? null, fn(Builder $query, array $status) => $query->whereIn('status', $status))
                            ->when($data['date_planifiee'] ?? null, fn(Builder $query, string $date) => $query->whereDate('date_planifiee', '=', $date))
                            ->when($data['date_prise_appel'] ?? null, fn(Builder $query, string $date) => $query->whereDate('date_prise_appel', '=', $date))
                            ->when($data['type'] ?? null, fn(Builder $query, string $type) => $query->where('type', '=', $type))
                            ->when($data['later'] ?? null, fn(Builder $query) => $query->where('status', '=', InterventionStatus::PLANIFIEE->value)
                                ->whereDate('date_planifiee', '<', now()))
                    )
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListInterventions::route('/'),
            'create' => Pages\CreateIntervention::route('/create'),
            'edit' => Pages\EditIntervention::route('/{record}/edit'),
            'view' => ViewIntervention::route('/{record}'),
        ];
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'cancell',
            'delete_any',
            'delete',
        ];
    }


    public static function buildInfolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                \Filament\Infolists\Components\View::make('components.report-header')
                    ->columnSpanFull()->viewData([
                        'numero' => $infolist->record->numero,
                        'date' => Carbon::parse($infolist->record->date_planifiee)->format('d/m/Y'),
                    ]),

                \Filament\Infolists\Components\View::make('filament.infolist.pages.view-intervention')
                    ->columnSpanFull(),
            ]);
    }
}
