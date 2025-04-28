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
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InterventionResource extends Resource
{
    protected static ?string $model = Intervention::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationGroup = 'Maintenance';
    protected static ?string $navigationLabel = 'Interventions';
    protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->schema([
                        Section::make('Informations sur l’intervention')
                            ->columns(2)
                            ->schema([
                                WidgetUtils::contractSelectWidget()
                                    ->columnSpanFull(),

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
                                    ->label('Description de la panne')
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

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->getStateUsing(fn($record) => InterventionStatus::from($record->status)->label())
                    ->searchable()
                    ->colors([
                        'warning' => "En cours",
                        'danger' => "Non commencée",
                        'primary' => "Annulée",
                        'success' => "Terminée",
                    ]),

                TextColumn::make('contract.customer.name')
                    ->label('Client')
                    ->searchable()
                    ->sortable()
                    ->extraAttributes(['class' => 'font-bold'])
                    ->limit(50),

                TextColumn::make('contract.generator.name')
                    ->label('Groupe Electrogène')
                    ->searchable()
                    ->sortable()
                    ->extraAttributes(['class' => 'font-bold'])
                    ->limit(50),

                TextColumn::make('contract.generator.modele')
                    ->label('Modele')
                    ->searchable()
                    ->sortable()
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
                        return $record->contract->generator->name . '-' . $record->contract->generator->modele . ' ' . $record->contract->generator->power . 'KVA - N/S: ' . $record->contract->generator->serial_number;
                    })->hiddenLabel()
                    ->size(10)
                    ->extraAttributes(['style' => 'font-weight: bold;font-size: 25px;'])
                    ->columnSpanFull(),
                \Filament\Infolists\Components\View::make('filament.infolist.pages.view-intervention')
                    ->columnSpanFull(),
            ]);
    }
}
