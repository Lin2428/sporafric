<?php

namespace App\Filament\Pages;

use App\Filament\Utils\BadgetWidget;
use App\Models\ContractGenerator;
use App\Models\DevisGenerator;
use App\Models\Generator;
use Filament\Pages\Page;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
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

    protected static function getBaseQuery(): Builder|Relation
    {
        return Generator::query();
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
                    ->extraAttributes(['style' => 'width: 60px;'])
                    ->updateStateUsing(function (string $state, $record) {
                        $record->houres = $state;
                        $record->next_vidange = $record->prochain_visite - $record->houres;
                        $record->vidange = $record->next_vidange > 250;
                        $record->save();
                        return $state;
                    }),
                     TextInputColumn::make('prochain_visite')
                    ->label('Prochaine visite')
                    ->extraAttributes(['style' => 'width: 60px;'])
                    ->updateStateUsing(function (string $state, $record) {
                        $record->prochain_visite = $state;
                        $record->next_vidange = $record->prochain_visite - $record->houres;
                        $record->vidange = $record->next_vidange > 250;
                        $record->save();
                        return $state;
                    }),

                

                TextColumn::make('next_vidange')->label('Prochaine vidange')->getStateUsing(fn($record) => $record->next_vidange . 'h'),
                TextColumn::make('vidange')
                    ->label('Vidange')
                    ->getStateUsing(function ($record) {
                        $text = $record->vidange ? 'Oui' : 'Non';
                        return BadgetWidget::boleanToBadget($record->vidange, 'Ok', 'Vidange');
                    })
                    ->html(),

               
            ])
            ->filters([
                //  Filter::make('status')
                // ->form([
                //     CheckboxList::make('status')
                //     ->options(collect(InterventionStatus::cases())
                //         ->mapWithKeys(fn($status) => [$status->value => $status->label()])
                //         ->toArray()),
                //     DatePicker::make('date_planifiee')
                //         ->label('Date planifiée'),
                //     DatePicker::make('date_prise_appel')
                //         ->label('Date de prise d\'appel'),
                //     Select::make('type')
                //         ->label('Type')
                //         ->options(collect(InterventionType::cases())
                //             ->mapWithKeys(fn($status) => [$status->value => $status->label()])
                //             ->toArray()),
                // ])
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
