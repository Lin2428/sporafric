<?php

namespace App\Filament\Resources;

use App\Enum\InterventionStatus;
use App\Enum\InterventionType;
use App\Filament\Resources\GeneratorResource\Pages\ViewIntervention;
use App\Filament\Resources\InterventionResource\Pages;
use App\Filament\Resources\InterventionResource\RelationManagers;
use App\Filament\Utils\InterventionUtil;
use App\Filament\Utils\WidgetUtils;
use App\Models\Intervention;
use Date;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;

class InterventionResource extends Resource
{
    protected static ?string $model = Intervention::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationGroup = 'Maintenance';
    protected static ?string $navigationLabel = 'Interventions';
    protected static ?int $navigationSort = 0;
    public static function getNavigationBadge(): ?string
    {
        $count = Intervention::count();
        return $count;
    }

    public static function customerColumn(Intervention $record): HtmlString
    {
        $html = "
                <div class='flex flex-col text-xs' style='line-height: 1.2;'>
                    <span class='text-[13px]'>{$record->contract?->customer->contact_c_phone}{$record->customer?->contact_c_phone}</span>
                    <span class='font-normal'>{$record->contract?->customerAdress->district?->name}{$record->customer?->customerAdresses[0]->district->name}</span>
                    <span class='font-normal' style='color: orange;'>{$record->contract?->customerAdress->city?->name}{$record->customer?->customerAdresses[0]->city->name}</span>
                </div>
            ";

        return new HtmlString($html);
    }

    public static function generatorColumn(Intervention $record): HtmlString
    {
        $html = "
                <div class='flex flex-col text-xs' style='line-height: 1.2;'>
                    <span class='font-normal'>{$record->contract?->generator->modele}{$record->generator}</span>
                    <span class='font-normal'>{$record->contract?->generator->serial_number}{$record->serial_number}</span>
                </div>
            ";

        return new HtmlString($html);
    }

    public static function form(Form $form): Form
    {

        return $form
            ->schema([
                Group::make()
                    ->schema([
                        Section::make('Informations sur l’intervention')
                            ->columns(2)
                            ->schema([
                                Select::make('type_location')
                                    ->label("Type de location")
                                    ->options(["1" => "Sous contrat", "0" => "Hors contrat"])
                                    ->columnSpanFull()
                                    ->reactive()
                                    ->required(),

                                WidgetUtils::contractSelectWidget()
                                    ->columnSpanFull()
                                    ->visible(fn(callable $get) => $get('type_location') == "1"),

                                Section::make('Information sur le client')
                                    ->columns(2)
                                    ->schema([
                                        WidgetUtils::customerSelectWidget()
                                            ->columnSpanFull(),

                                        TextInput::make('generator')
                                            ->label("Marque du GE")
                                            ->required(),
                                        TextInput::make('power')
                                            ->label("Puissance (KVA)")
                                            ->numeric(),
                                        TextInput::make('serial_number')
                                            ->label("Numéro de série")->columnSpanFull(),
                                    ])->visible(fn(callable $get) => $get('type_location') == "0"),

                                DatePicker::make('date_prise_appel')
                                    ->label('Date de prise d’appel')
                                    ->required(),

                                DatePicker::make('date_planifiee')
                                    ->label('Date planifiée')
                                    ->required(),

                                TextInput::make('identifiant')
                                    ->label('Numéro de Bon d\'intervention')
                                    ->required(),

                                Select::make('type')
                                    ->label('Type')
                                    ->options(collect(InterventionType::cases())
                                        ->mapWithKeys(fn($status) => [$status->value => $status->label()])
                                        ->toArray())
                                    ->required(),

                                Textarea::make('description_panne')
                                    ->label('Description de la panne ou du travail à effectuer')
                                    ->required()
                                    ->rows(5)
                                    ->columnSpanFull(),
                            ])
                    ])->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([InterventionUtil::infoInterne()])->columnSpan(['lg' => 1]),

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
                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime("d/m/Y à H:i")
                    ->sortable(),

                TextColumn::make('identifiant')
                    ->label('Numéro')
                    ->searchable()
                    ->sortable()
                    ->limit(50),

                TextColumn::make('type_location')
                    ->label('Type location')
                    ->getStateUsing(fn($record) => $record->type_location == 1 ? "Sous contrat" : "Hors contract")
                    ->extraAttributes(['class' => 'font-bold']),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->getStateUsing(fn($record) => InterventionStatus::from($record->status)->label())
                    ->searchable()
                    ->colors([
                        'warning' => "En cours",
                        'info' => "Non commencée",
                        'danger' => "Annulée",
                        'success' => "Terminée",
                    ]),

                TextColumn::make('client') // Nom arbitraire, car on utilise getStateUsing
                    ->label('Client')
                    ->searchable()
                    ->sortable()
                    ->getStateUsing(function (Intervention $record) {
                        return $record->contract
                            ? optional($record->contract->customer)->name
                            : optional($record->customer)->name;
                    })
                    ->description(fn(Intervention $record) => static::customerColumn($record))
                    ->extraAttributes(['class' => 'font-bold'])
                    ->limit(50),

                TextColumn::make('groupe')
                    ->label('Groupe Électrogène')
                    ->searchable()
                    ->sortable()
                    ->getStateUsing(function (Intervention $record) {
                        return $record->contract
                            ? optional($record->contract->generator)->name
                            : $record->generator;
                    })
                    ->description(fn(Intervention $record) => static::generatorColumn($record))
                    ->extraAttributes(['class' => 'font-bold'])
                    ->limit(50),

                TextColumn::make('type')
                    ->label('Type')
                    ->getStateUsing(fn($record) => InterventionType::from($record->type)->label())
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('cancel')
                    ->label("Annuler")
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->requiresConfirmation(),
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
    public static function buildInfolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([

                TextEntry::make('generator')
                    ->getStateUsing(function (Intervention $record) {
                        return $record->contract != null ? $record->contract->generator->name . '-' . $record->contract->generator->modele . ' ' . $record->contract->generator->power . 'KVA - N/S: ' . $record->contract->generator->serial_number : $record->generator . '-' . $record->power . 'KVA ' . $record->serial_number;
                    })->hiddenLabel()
                    ->size(10)
                    ->extraAttributes(['style' => 'font-weight: bold;font-size: 25px;'])
                    ->columnSpanFull(),
                \Filament\Infolists\Components\View::make('filament.infolist.pages.view-intervention')
                    ->columnSpanFull(),
            ]);
    }
}
