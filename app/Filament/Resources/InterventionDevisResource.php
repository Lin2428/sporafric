<?php

namespace App\Filament\Resources;

use App\Enum\InterventionStatus;
use App\Enum\InterventionType;
use App\Filament\Resources\GeneratorResource\Pages\ViewInterventionDevis;
use App\Filament\Resources\InterventionDevisResource\Pages;
use App\Filament\Resources\InterventionDevisResource\RelationManagers;
use App\Filament\Utils\InterventionUtil;
use App\Filament\Utils\WidgetUtils;
use App\Models\Intervention;
use App\Models\InterventionDevis;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
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

                                WidgetUtils::contractSelectWidget('devis_id')
                                    ->columnSpanFull()
                                    ->reactive()
                                    ->label("Devis"),

                                 WidgetUtils::generatorSelectWidget(isDispo:false)
                                    ->columnSpanFull()
                                    ->reactive()
                                    ->visible(fn(callable $get) => $get('devis_id') != null),

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
        ->query(static::getEloquentQuery()->where('type_service', 0))
        ->defaultPaginationPageOption(50)
        ->columns(InterventionUtil::table("Devis"))
        ->defaultSort('date_planifiee', 'desc')
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
                        return $record->devis != null ? $record->generator?->name . '-' . $record->generator?->power . ' KVA ' . $record->generator?->serial_number:"";
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

