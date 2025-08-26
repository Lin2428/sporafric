<?php

namespace App\Filament\Resources;

use App\Enum\InterventionStatus;
use App\Enum\InterventionType;
use App\Filament\Resources\RevisionResource\Pages;
use App\Filament\Resources\RevisionResource\RelationManagers;
use App\Filament\Utils\InterventionUtil;
use App\Filament\Utils\WidgetUtils;
use App\Models\Generator;
use App\Models\Intervention;
use App\Models\Piece;
use App\Models\Technicien;
use App\Utils\NumberUtils;
use ArielMejiaDev\FilamentPrintable\Actions\PrintBulkAction;
use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Filament\Forms;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RevisionResource extends Resource
{
    protected static ?string $model = Intervention::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        $onUpdate = function (Set $set, Get $get) {
            $pieces = $get('../../pieces');

            if ($pieces) {
                foreach ($pieces as $piece) {
                    $price = Piece::find($piece['piece_id'])?->pr;

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
                                    ->label("Type")
                                    ->default(2)
                                    ->disabled()
                                    ->options(["1" => "Maintenance", "0" => "Location", "2" => "Conso interne"])

                                    ->columnSpanFull()
                                    ->reactive()
                                    ->required(),

                                TextInput::make('numero')
                                    ->label('Numéro')
                                    ->default(NumberUtils::intevention_numero('INT-CINT'))
                                    ->disabled()
                                    ->columnSpanFull(),

                                WidgetUtils::contractSelectWidget('devis_id')
                                    ->columnSpanFull()
                                    ->reactive()
                                    ->required()
                                    ->label("Devis"),

                                WidgetUtils::generatorSelectWidget(isDispo: false, isgetAll: true, onUpdate: function (Set $set, $state) {
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

                                DateTimePicker::make('date_planifiee')
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
                                    ->reactive(),

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

                Select::make('techniciens')
                    ->options(Technicien::all()->pluck(['id' => 'name']))
                    ->label('Techniciens assignés')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->formatStateUsing(function ($record) {
                        if (empty($record->interventionTechniciens)) return [];
                     
                        return $record->interventionTechniciens?->map(function ($technicien) {
                            return [$technicien->id];
                        })->toArray();
                    })
                    ->placeholder('Sélectionner un technicien'),
                        Section::make('Pièces jointes')
                            ->columns(2)
                            ->schema([
                                // TextInput::make('montant')
                                //     ->label('Montant de la main d\'oeuvre')
                                //     ->columnSpanFull(),

                                Repeater::make('fiches')
                                    ->label('')
                                    ->relationship()
                                    ->addActionLabel('Ajouter une pièce jointe')
                                   ->dehydrated(true)
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
                    ->emptyLabel('Aucune pièce livrée')
                    ->label('Pièces livrées')
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

    public static function table(Table $table): Table
    {
        return $table
            ->query(static::$model::where('type_service', '=', 2))
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
                        fn(Builder $query, array $data) => $query
                            ->when($data['status'] ?? null, fn(Builder $query, array $status) => $query->whereIn('status', $status))
                            ->when($data['date_planifiee'] ?? null, fn(Builder $query, string $date) => $query->whereDate('date_planifiee', '=', $date))
                            ->when($data['date_prise_appel'] ?? null, fn(Builder $query, string $date) => $query->whereDate('date_prise_appel', '=', $date))
                            ->when($data['type'] ?? null, fn(Builder $query, string $type) => $query->where('type', '=', $type))
                    )
            ])
            ->actions([
                 Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                    ->url(fn($record) => url('/admin/intervention-devis/' . $record->id)),

                    Tables\Actions\EditAction::make()
                    ->modalHeading('Modifier le devis')
                    ->modalWidth('6xl')
                    ->action(function ($data,$record) {
                        
                        $record->update($data);

                        $submittedPieces = collect($data['pieces'])->pluck('piece_id')->toArray();
                        $techniciens = collect($data['techniciens'])->toArray();

                         $record->interventionTechniciens()
                            ->whereNotIn('technicien_id', $techniciens)
                            ->delete();
                        
                        foreach ($data['techniciens'] as $technicien) {
                        $record->interventionTechniciens()->syncWithoutDetaching(
                            $technicien
                        );
                    }

                        $record->pieces()
                            ->whereNotIn('piece_id', $submittedPieces)
                            ->delete();

                        foreach ($data['pieces'] as $piece) {
                            $record->pieces()->syncWithoutDetaching([
                                $piece['piece_id'] => [
                                    'qty'          => $piece['qty'],
                                    'price'        => $piece['price'] ?? 0,
                                    'generator_id' => $data['generator_id'] ?? null,
                                ],
                            ]);
                        }

                        $houres = $data['houres'];
                        $nexTvidange = $data['prochain_visite'] -  $houres;
                        $vidange =  $nexTvidange > 30;

                        Generator::where('id', $data['generator_id'])
                            ->update([
                                'houres' => $data['houres'],
                                'next_vidange' => $nexTvidange,
                                'prochain_visite' => $data['prochain_visite'],
                                'vidange' => $vidange
                            ]);
                    }),
                    
                ]), 
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    PrintBulkAction::make(),
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
            'index' => Pages\ListRevisions::route('/'),
            // 'create' => Pages\CreateRevision::route('/create'),
            // 'edit' => Pages\EditRevision::route('/{record}/edit'),
        ];
    }
}
