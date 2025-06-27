<?php

namespace App\Filament\Pages;

use App\Filament\Utils\BadgetWidget;
use App\Models\ContractGenerator;
use App\Models\DevisGenerator;
use App\Models\Generator;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class GeneratorHours extends Page implements HasTable
{
    use InteractsWithTable;
    protected static ?string $navigationIcon = 'heroicon-o-arrow-path-rounded-square';
    protected static ?string $navigationGroup = 'Ronde';
    protected static ?string $navigationLabel = 'Vidanges';
    protected static ?string $title = 'Vidanges';

    protected static function getBaseQuery(): Builder|Relation
    {
        return Generator::query()
        ->orderBy('vidange');
    }

          public static function getNavigationBadge(): ?string
    {
        $count = Generator::where('vidange', false)->count();
        return $count;
    }

    public static function customerColumn(ContractGenerator|DevisGenerator|null $record): HtmlString
    {
        if (!$record) {
            return new HtmlString('');
        }
        $html = "
                <div class='flex flex-col text-xs' style='line-height: 1.2;'>
                    <span class='text-[13px]'>{$record->contract?->customer->contact_c_phone}{$record->customer?->contact_c_phone}</span>
                </div>
            ";

        return new HtmlString($html);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(static::getBaseQuery())
            ->defaultSort('created_at', 'desc')
            ->defaultPaginationPageOption('all')
            ->columns([
                TextColumn::make('name')->label('GE')->limit(8),
                TextColumn::make('reference'),
                TextColumn::make('client') // Nom arbitraire, car on utilise getStateUsing
                    ->label('Client')
                    ->searchable(true, function ($search) {
                        return fn($query, $search) => $query
                            ->whereHas('devisGenerator.devis.customer', function ($query) use ($search) {
                                $query->where('name', 'like', "%{$search}%");
                            })
                            ->orWhereHas('ContractGenerator.contract.customer', function ($query) use ($search) {
                                $query->where('name', 'like', "%{$search}%");
                            });
                    })
                    ->getStateUsing(function ($record) {
                        return optional($record->devisGenerator?->devis?->customer)->name ?? optional($record->ContractGenerator?->contract?->customer)->name;
                    })
                    ->description(fn($record) => static::customerColumn($record->devisGenerator ?? $record->ContractGenerator))
                    ->extraAttributes(['class' => 'font-bold'])
                    ->limit(8),

                TextInputColumn::make('houres')
                    ->label('Rélévé des heures')
                    ->width(50)
                    ->type('number')
                    ->updateStateUsing(function (string $state, $record) {
                        $record->houres = $state;
                        $record->next_vidange = $record->prochain_visite - $record->houres;
                        $record->vidange = $record->next_vidange > 30;
                        $record->save();
                        return $state;
                    }),

                     TextInputColumn::make('prochain_visite')
                    ->label('Prochaine visite')
                    ->extraAttributes(['style' => 'width: 100px;'])
                    ->type('number')
                    ->updateStateUsing(function (string $state, $record) {
                        $record->prochain_visite = $state;
                        $record->next_vidange = $record->prochain_visite - $record->houres;
                        $record->vidange = $record->next_vidange > 30;
                        $record->save();
                        return $state;
                    }),

                

                TextColumn::make('next_vidange')->label('Prochaine vidange')->getStateUsing(fn($record) => $record->next_vidange . 'h'),
                TextColumn::make('vidange')
                    ->label('Vidange')
                    ->getStateUsing(function ($record) {
                        return BadgetWidget::boleanToBadget($record->vidange, 'Ok', 'Vidange');
                    })
                    ->html(),

               
            ])
            ->filters([
                 Filter::make('status')
                ->form([
                    Radio::make('vidange')
                        ->label('Statut')
                        ->options([
                            2 => 'Vidange',
                            1 => 'Ok',
                        ])
                ])->query(function (Builder $query, $data) {
                    $query->when($data['vidange'] ?? null, function (Builder $query, $vidange) {
                        $vidange = $vidange == 2 ? false : true;
                        $query->where('vidange','=',$vidange);
                    });
                })
            ])
            ->actions([
                //     ActionGroup::make([
                //        ViewAction::make()
                //        ->url(fn($record) => url('admin/interventions/'.$record->id)),
                //         EditAction::make()
                //         ->url(fn($record) => url('admin/interventions/'.$record->id.'/edit')),
                //   Action::make('cancel')
                //         ->label("Annuler")
                //         ->color('danger')
                //         ->icon('heroicon-o-x-circle')
                //         ->requiresConfirmation(),
                //     ]),
            ])
            ->bulkActions([]);
    }

    protected static string $view = 'filament.pages.generator-hours';
}
