<?php

namespace App\Filament\Resources;

use App\Enum\InterventionStatus;
use App\Enum\InterventionType;
use App\Filament\Resources\GeneratorResource\Pages\ViewIntervention;
use App\Filament\Resources\InterventionResource\Pages;
use App\Filament\Utils\InterventionUtil;
use App\Filament\Utils\WidgetUtils;
use App\Models\Intervention;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
class InterventionResource extends Resource
{
    protected static ?string $model = Intervention::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationGroup = 'Maintenance';
    protected static ?string $navigationLabel = 'Interventions';
    protected static ?int $navigationSort = 0;
    public static function getNavigationBadge(): ?string
    {
        $count = Intervention::where('type_service', 1)->count();
        return $count;
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
                                Select::make('type_service')
                                    ->label("Location ou Maintenance ?")
                                    ->options(["1" => "Maintenance", "0" => "Location"])
                                    ->default("1")
                                    ->disabled()
                                    ->columnSpanFull()
                                    ->reactive(),

                                Select::make('type_activite')
                                    ->label("Type d'activité")
                                    ->options([1 => "Sous contrat", 0 => "Hors contrat"])
                                    ->columnSpanFull()
                                    ->reactive(),

                                WidgetUtils::contractSelectWidget()
                                    ->columnSpanFull()
                                    ->reactive()
                                    ->visible(fn(callable $get) => $get('type_activite') == "1"),

                                WidgetUtils::generatorSelectWidget(type: 2)
                                    ->columnSpanFull()
                                    ->visible(fn(callable $get) => $get('contract_id') != null && $get('type_activite') == "1"),

                                Section::make('Information sur le client')
                                    ->columns(2)
                                    ->schema([
                                        WidgetUtils::customerSelectWidget()
                                            ->columnSpanFull(),

                                        TextInput::make('generator_name')
                                            ->label("Marque du GE"),
                                        TextInput::make('power')
                                            ->label("Puissance (KVA)")
                                            ->numeric(),
                                        TextInput::make('serial_number')
                                            ->label("Numéro de série")->columnSpanFull(),
                                    ])->visible(fn(callable $get) => $get('type_activite') == "0"),
                                

                                DatePicker::make('date_prise_appel')
                                    ->label('Date de prise d’appel'),

                                DatePicker::make('date_planifiee')
                                    ->label('Date planifiée'),

                                TextInput::make('identifiant')
                                ->unique(ignoreRecord:true)
                                    ->label('Numéro de Bon d\'intervention'),

                                Select::make('type')
                                    ->label('Type')
                                    ->options(collect(InterventionType::cases())
                                        ->mapWithKeys(fn($status) => [$status->value => $status->label()])
                                        ->toArray()),

                                Textarea::make('description_panne')
                                    ->label('Description de la panne ou du travail à effectuer')
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
        ->query(static::getEloquentQuery()->where('type_service', 1))
        ->defaultSort('date_planifiee', 'desc')
        ->columns(InterventionUtil::table())
            ->filters([
                Filter::make('status')
                ->form([
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
                    fn (Builder $query, array $data) => $query
                        ->when($data['status'] ?? null, fn (Builder $query, array $status) => $query->whereIn('status', $status))
                        ->when($data['date_planifiee'] ?? null, fn (Builder $query, string $date) => $query->whereDate('date_planifiee', '=', $date))
                        ->when($data['date_prise_appel'] ?? null, fn (Builder $query, string $date) => $query->whereDate('date_prise_appel', '=', $date))
                        ->when($data['type'] ?? null, fn (Builder $query, string $type) => $query->where('type', '=', $type))
                        ->when($data['later'] ?? null, fn (Builder $query) => $query->where('status', '=', InterventionStatus::PLANIFIEE->value)
                        ->whereDate('date_planifiee', '<', now()))
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
                       return $record->contract != null ? $record->generator?->name . ' ' . $record->generator?->power . 'KVA - N/S: ' . $record?->generator?->serial_number : $record->generator_name . '-' . $record->power . 'KVA - N/S: ' . $record->serial_number;
                    })->hiddenLabel()
                    ->size(10)
                    ->extraAttributes(['style' => 'font-weight: bold;font-size: 25px;'])
                    ->columnSpanFull()
                    ->url(fn (Intervention $record): ?string => $record->generator?->id ? url('admin/contract-generators', ['record' => $record->generator->id]) : null),
                \Filament\Infolists\Components\View::make('filament.infolist.pages.view-intervention')
                    ->columnSpanFull(),
            ]);
    }
}
