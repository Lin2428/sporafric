<?php

namespace App\Filament\Pages;

use App\Filament\Utils\BadgetWidget;
use App\Models\ContractGenerator;
use App\Models\DevisGenerator;
use App\Models\Generator;
use ArielMejiaDev\FilamentPrintable\Actions\PrintAction;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
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

    public static function canAccess(): bool
    {
        return auth()->user()->hasPermissionTo('page_GeneratorHours');
    }

    protected static function getBaseQuery(): Builder|Relation
    {
        return Generator::query()
            ->orderBy('name');
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
    // protected function getHeaderActions(): array
    // {
    //     return [
    //         // Actions\CreateAction::make(),
    //         PrintAction::make(),
    //     ];
    // }
    public static function table(Table $table): Table
    {
        return $table
            ->query(static::getBaseQuery())
            ->defaultSort('created_at', 'desc')
            ->defaultPaginationPageOption('all')
            ->columns([
                TextColumn::make('name')->label('GE')
                    ->limit(20)
                    ->copyable()
                    ->searchable(),
                // TextColumn::make('reference')
                //     ->searchable(),
                TextColumn::make('client')
                    ->label('Client')
                    ->searchable(true, function ($query, $search) {
                        return $query
                            ->where(function ($query) use ($search) {
                                $query->whereHas('devisGenerator.devis.customer', function ($query) use ($search) {
                                    $query->where('name', 'like', "%$search%");
                                })
                                    ->orWhereHas('contractGenerator.contract.customer', function ($query) use ($search) {
                                        $query->where('name', 'like', "%$search%");
                                    });
                            });
                    })
                    ->getStateUsing(function ($record) {
                        return optional($record->devisGenerator?->devis?->customer)->name ?? optional($record->ContractGenerator?->contract?->customer)->name;
                    })
                    ->description(fn($record) => static::customerColumn($record->devisGenerator ?? $record->ContractGenerator))
                    ->extraAttributes(['class' => 'font-bold'])
                    ->limit(8),

                TextColumn::make('site')
                    ->label('Site')
                    ->getStateUsing(function ($record) {
                        return optional($record->devisGenerator)?->site ?? optional($record->ContractGenerator)?->site;
                    })
                    ->limit(15)
                    ->searchable(true, function ($query, $search) {
                        return  $query
                            ->where(function ($query) use ($search) {
                                $query->whereHas('devisGenerator', function ($query) use ($search) {
                                    $query->where('site', 'like', "%$search%");
                                })
                                    ->orWhereHas('contractGenerator', function ($query) use ($search) {
                                        $query->where('site', 'like', "%$search%");
                                    });
                            });
                    }),

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

                TextColumn::make('prochain_visite')
                    ->label('Vidange programmée')
                    ->extraAttributes(['style' => 'width: 100px;']),



                TextColumn::make('next_vidange')
                    ->label('Temps avant vidange')
                    ->getStateUsing(fn($record) => $record->next_vidange . 'h'),

                TextColumn::make('vidange')
                    ->label('Vidange')
                    ->getStateUsing(function ($record) {
                        if ($record->vidange !== null) {
                            return BadgetWidget::boleanToBadget($record->vidange, 'Ok', 'Vidange');
                        }
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
                            ]),
                        TextInput::make('search')
                            ->label('Rechercher')
                            ->placeholder('Rechercher par client, site, etc.')
                            ->live(true),
                    ])->query(function (Builder $query, $data) {
                        $query->when($data['vidange'] ?? null, function (Builder $query, $vidange) {
                            $vidange = $vidange == 2 ? false : true;
                            $query->where('vidange', '=', $vidange);
                        })
                            ->when($data['search'] ?? null, function (Builder $query, $search) {
                                $query->whereHas('devisGenerator', function ($query) use ($search) {
                                    $query->where('site', 'like', "%$search%");
                                })
                                    ->orWhereHas('contractGenerator', function ($query) use ($search) {
                                        $query->where('site', 'like', "%$search%");
                                    })
                                    ->orWhereHas('devisGenerator.devis.customer', function ($query) use ($search) {
                                        $query->where('name', 'like', "%$search%");
                                    })
                                    ->orWhereHas('contractGenerator.contract.customer', function ($query) use ($search) {
                                        $query->where('name', 'like', "%$search%");
                                    });
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
