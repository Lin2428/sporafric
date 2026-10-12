<?php

namespace App\Filament\Resources;

use App\Enum\HourRoundStatus;
use App\Filament\Resources\HourRoundResource\Pages;
use App\Filament\Resources\HourRoundResource\RelationManagers;
use App\Models\Customer;
use App\Models\Generator;
use App\Models\HourRound;
use App\Models\Technicien;
use App\Services\HourReadingService;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Tournées de relevés d'heures (remplace la feuille papier « Impression de fiche ») :
 * le bureau assigne une liste de groupes à un technicien, qui saisit les HF dans la PWA.
 */
class HourRoundResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = HourRound::class;

    protected static ?string $label = 'Tournée de relevés';

    protected static ?string $pluralLabel = 'Relevés d\'heures';

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationGroup = 'Ronde';

    protected static ?string $navigationLabel = 'Relevés d\'heures';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Tournée')
                ->columns(2)
                ->schema([
                    Select::make('technicien_id')
                        ->label('Technicien')
                        ->options(Technicien::query()->orderBy('name')->pluck('name', 'id'))
                        ->searchable()
                        ->required(),

                    DatePicker::make('date')
                        ->label('Date de la tournée')
                        ->default(now())
                        ->required(),

                    Textarea::make('notes')
                        ->label('Consignes')
                        ->rows(2)
                        ->columnSpanFull(),
                ]),

            Section::make('Groupes à relever')
                ->visibleOn('create')
                ->schema(static::generatorPickerSchema()),
        ]);
    }

    /**
     * Sélection client(s) → groupes (tous cochés par défaut, on décoche ceux à exclure).
     */
    public static function generatorPickerSchema(): array
    {
        return [
            Select::make('customers')
                ->label('Client')
                ->options(Customer::query()->orderBy('name')->pluck('name', 'id'))
                ->multiple()
                ->searchable()
                ->live()
                ->afterStateUpdated(function ($state, Set $set) {
                    $set('generator_ids', HourReadingService::generatorsForCustomers($state ?? [])->pluck('id')->all());
                })
                ->required(),

            CheckboxList::make('generator_ids')
                ->label('Groupes électrogènes')
                ->options(fn (Get $get) => HourReadingService::generatorsForCustomers($get('customers') ?? [])
                    ->mapWithKeys(fn (Generator $g) => [$g->id => HourReadingService::generatorLabel($g)])
                    ->all())
                ->bulkToggleable()
                ->searchable()
                ->required()
                ->visible(fn (Get $get) => filled($get('customers'))),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->with('technicien:id,name')
                ->withCount([
                    'readings',
                    'readings as done_count' => fn ($q) => $q->whereNotNull('houres'),
                ]))
            ->defaultSort('date', 'desc')
            ->defaultPaginationPageOption(50)
            ->columns([
                TextColumn::make('date')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('technicien.name')
                    ->label('Technicien')
                    ->searchable()
                    ->extraAttributes(['class' => 'font-bold']),

                TextColumn::make('progress')
                    ->label('Relevés')
                    ->getStateUsing(fn ($record) => "{$record->done_count} / {$record->readings_count}"),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn ($state) => HourRoundStatus::tryFrom((string) $state)?->label())
                    ->color(fn ($state) => HourRoundStatus::tryFrom((string) $state)?->color()),

                TextColumn::make('notes')
                    ->label('Consignes')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('technicien_id')
                    ->label('Technicien')
                    ->options(Technicien::query()->orderBy('name')->pluck('name', 'id')),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options(collect(HourRoundStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()])),
            ])
            ->actions([
                Tables\Actions\Action::make('print')
                    ->label('Imprimer')
                    ->icon('heroicon-o-printer')
                    ->color('info')
                    ->url(fn ($record) => static::getUrl('print', ['record' => $record])),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ReadingsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListHourRounds::route('/'),
            'create' => Pages\CreateHourRound::route('/create'),
            'edit'   => Pages\EditHourRound::route('/{record}/edit'),
            'print'  => Pages\PrintHourRound::route('/{record}/print'),
        ];
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete_any',
            'delete',
        ];
    }
}
