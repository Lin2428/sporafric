<?php

namespace App\Filament\Resources;

use App\Enum\InterventionStatus;
use App\Enum\InterventionType;
use App\Filament\Resources\GeneratorResource\Pages\ViewInterventionDevis;
use App\Filament\Resources\InterventionDevisResource\Pages;
use App\Filament\Resources\InterventionDevisResource\RelationManagers;
use App\Filament\Utils\InterventionUtil;
use App\Filament\Utils\WidgetUtils;
use App\Models\Generator;
use App\Models\Intervention;
use App\Models\InterventionDevis;
use App\Models\Piece;
use App\Utils\NumberUtils;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
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
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InterventionDevisResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Intervention::class;

    protected static ?string $label           = "Interventions Location";
    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationGroup = 'Location';
    protected static ?string $navigationLabel = 'Interventions';
    protected static ?string $title = 'Interventions';
    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        $count = Intervention::where('type_service',  0)->count();
        return $count;
    }


public static function form(Form $form): Form
    {
         $onUpdate = function(Set $set, Get $get) {
            $pieces = $get('../../pieces');

           if ($pieces) {
                foreach ($pieces as $piece) {
                $price = Piece::find($piece['piece_id'])?->pv;

                $set('price', $price?? 0);
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
                                    ->default(0)
                                    ->disabled()
                                    ->options(["1" => "Maintenance", "0" => "Location"])
                                    
                                    ->columnSpanFull()
                                    ->reactive()
                                    ->required(),
                                
                                TextInput::make('numero')
                                    ->label('Numéro')
                                    ->default(NumberUtils::intevention_numero('INT-LOC'))
                                    ->required()
                                    ->unique(Intervention::class, 'numero', ignoreRecord: true)
                                    ->columnSpanFull(),

                                WidgetUtils::contractSelectWidget('devis_id')
                                    ->columnSpanFull()
                                    ->reactive()
                                    ->required()
                                    ->label("Devis"),

                                 WidgetUtils::generatorSelectWidget(isDispo:false, onUpdate: function (Set $set, $state) {
                                        $generator = Generator::find($state);

                                        $set('houres', $generator?->houres);
                                        $set('next_vidange', $generator?->next_vidange);
                                        $set('prochain_visite', $generator?->prochain_visite);
                                    })
                                    ->columnSpanFull()
                                    ->reactive()
                                    ->required()
                                    ->visible(fn(callable $get) => $get('devis_id') != null),

                                DatePicker::make('date_prise_appel')
                                    ->label('Date de prise d’appel')
                                    ->default(now())
                                    ->required(),

                                DatePicker::make('date_planifiee')
                                    ->label('Date planifiée'),

                                TextInput::make('identifiant')
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
                                    ->reactive()
                                    ->visible(fn(callable $get) => $get('type') == InterventionType::VIDANGE->value ||$get('type') == InterventionType::RONDE->value),

                               TextInput::make('next_vidange')
                                    ->numeric()
                                    ->reactive()
                                    ->label('Prochaine vidange')
                                    ->formatStateUsing(function (Get $get) {
                                        $generator = Generator::find($get('generator_id'));
                                  
                                        return $generator?->next_vidange;
                                    })
                                       ->visible(fn(callable $get) => $get('type') == InterventionType::VIDANGE->value ||$get('type') == InterventionType::RONDE->value),

                                        TextInput::make('prochain_visite')
                                    ->numeric()
                                    ->reactive()
                                    ->label('Prochaine vidange')
                                    ->formatStateUsing(function (Get $get) {
                                        $generator = Generator::find($get('generator_id'));
                                  
                                        return $generator?->prochain_visite;
                                    })
                                    ->columnSpanFull()
                                    ->visible(fn(callable $get) => $get('type') == InterventionType::VIDANGE->value ||$get('type') == InterventionType::RONDE->value),


                                Textarea::make('description_panne')
                                    ->label('Description de la panne ou du travail à effectuer')
                                    ->rows(5)
                                    ->columnSpanFull(),
                            ])
                    ])->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        InterventionUtil::infoInterne(),
                        Section::make('Autre information')
                            ->columns(2)
                            ->schema([
                                TextInput::make('montant')
                                    ->label('Montant')
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
                                            ->columnSpanFull(),
                                    ])->columnSpanFull(),
                            ]),
                        ])->columnSpan(['lg' => 1]),


                Section::make('Pièces livrées')
                    ->columns(2)
                    ->schema([
                        Repeater::make('pieces')
                    ->label('')
                    ->formatStateUsing(function ($record) {
                        if(empty($record->pieces)) return [];
                      
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
                            ->columnSpanFull()
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
                    ->grid(2)
                    ->columns(2)
                    ])->columnSpanFull(),

            ])->columns(3);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return static::buildInfolist($infolist);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->query(static::getEloquentQuery()->where('type_service', 0))
        ->defaultPaginationPageOption(50)
        ->defaultSort('created_at', 'desc')
        ->columns(InterventionUtil::table("Devis"))
            ->filters([
                Filter::make('status')
                ->form([
                    CheckboxList::make('status')
                    ->options(collect(InterventionStatus::cases())
                        ->mapWithKeys(fn($status) => [$status->value => $status->label()])
                        ->toArray()),

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
                    fn (Builder $query, array $data) => $query
                        ->when($data['status'] ?? null, fn (Builder $query, array $status) => $query->whereIn('status', $status))
                        ->when($data['date_planifiee'] ?? null, fn (Builder $query, string $date) => $query->whereDate('date_planifiee', '=', $date))
                        ->when($data['date_prise_appel'] ?? null, fn (Builder $query, string $date) => $query->whereDate('date_prise_appel', '=', $date))
                        ->when($data['type'] ?? null, fn (Builder $query, string $type) => $query->where('type', '=', $type))
                )
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('cancel')
                    ->label("Annuler")
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->requiresConfirmation(),
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
            'index' => Pages\ListInterventionDevis::route('/'),
            'create' => Pages\CreateInterventionDevis::route('/create'),
            'edit' => Pages\EditInterventionDevis::route('/{record}/edit'),
                'view' =>  ViewInterventionDevis::route('/{record}'),
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

                TextEntry::make('generator')
                    ->getStateUsing(function (Intervention $record) {
                        return $record->devis != null ? $record->generator?->name . '-' . $record->generator?->power . ' kVA ' . $record->generator?->serial_number:"";
                    })->hiddenLabel()
                    ->size(10)
                    ->extraAttributes(['style' => 'font-weight: bold;font-size: 25px;'])
                    ->columnSpanFull()
                    ->url(fn(Intervention $record) => url('/admin/generators/' . $record->generator?->id)),
                \Filament\Infolists\Components\View::make('filament.infolist.pages.view-intervention')
                    ->columnSpanFull(),
            ]);
    }
}

