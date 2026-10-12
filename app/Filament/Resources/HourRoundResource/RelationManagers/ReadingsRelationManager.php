<?php

namespace App\Filament\Resources\HourRoundResource\RelationManagers;

use App\Filament\Resources\HourRoundResource;
use App\Services\HourReadingService;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ReadingsRelationManager extends RelationManager
{
    protected static string $relationship = 'readings';

    protected static ?string $title = 'Groupes à relever';

    protected static ?string $icon = 'heroicon-o-bolt';

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('houres')
                ->label('Heures de fonctionnement (HF)')
                ->numeric()
                ->integer()
                ->minValue(0)
                ->required(),
            Textarea::make('note')
                ->label('Note')
                ->rows(2),
        ]);
    }

    public function table(Table $table): Table
    {
        $isBelowPrevious = fn ($record) => $record->houres !== null
            && $record->previous_houres !== null
            && $record->houres < $record->previous_houres;

        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with([
                'customer:id,name',
                'generator' => fn ($q) => $q->without(['contractGenerator', 'etat', 'files']),
            ]))
            ->paginated(false)
            ->columns([
                TextColumn::make('customer.name')->label('Client')->limit(20),
                TextColumn::make('site')->label('Site')->limit(20)->color('primary'),
                TextColumn::make('code_site')->label('Code'),
                TextColumn::make('generator.name')->label('GE')->extraAttributes(['class' => 'font-bold']),
                TextColumn::make('generator.power')->label('P (kVA)')->alignRight(),
                TextColumn::make('previous_houres')->label('HF préc.')->alignRight()->placeholder('—'),
                TextColumn::make('houres')
                    ->label('HF')
                    ->alignRight()
                    ->weight('bold')
                    ->placeholder('À relever')
                    ->color(fn ($record) => $isBelowPrevious($record) ? 'danger' : null)
                    ->tooltip(fn ($record) => $isBelowPrevious($record) ? 'Inférieur au relevé précédent' : null),
                TextColumn::make('vp')
                    ->label('VP')
                    ->alignRight()
                    ->getStateUsing(fn ($record) => $record->prochain_visite ?? $record->generator?->prochain_visite),
                TextColumn::make('tav')
                    ->label('TAV')
                    ->alignRight()
                    ->getStateUsing(fn ($record) => $record->next_vidange ?? $record->generator?->next_vidange)
                    ->formatStateUsing(fn ($state) => $state !== null ? "{$state} h" : null)
                    ->color(fn ($state) => match (true) {
                        $state === null => null,
                        $state <= 5     => 'danger',
                        $state <= 30    => 'warning',
                        default         => 'success',
                    }),
                TextColumn::make('read_at')->label('Relevé le')->dateTime('d/m H:i')->placeholder('—'),
                TextColumn::make('note')->label('Note')->limit(25)->toggleable(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('addGenerators')
                    ->label('Ajouter des groupes')
                    ->icon('heroicon-o-plus')
                    ->form(HourRoundResource::generatorPickerSchema())
                    ->action(function (array $data) {
                        HourReadingService::addGenerators($this->getOwnerRecord(), $data['generator_ids'] ?? []);
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Saisir HF')
                    ->modalHeading(fn ($record) => "Relevé — {$record->generator?->name}")
                    ->using(fn ($record, array $data) => HourReadingService::record(
                        $record,
                        (int) $data['houres'],
                        now(),
                        $data['note'] ?? null,
                        auth()->id(),
                    )),
                Tables\Actions\DeleteAction::make()
                    ->label('Retirer')
                    ->after(fn () => $this->getOwnerRecord()->refreshStatus()),
            ])
            ->bulkActions([]);
    }
}
