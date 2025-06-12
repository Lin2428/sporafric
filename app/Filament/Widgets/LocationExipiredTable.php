<?php

namespace App\Filament\Widgets;

use App\Models\Devis;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LocationExipiredTable extends BaseWidget
{
    protected static ?string $heading = 'Locations Terminées';
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Devis::query()->where('is_active', true)
                    ->where('end_date', '<=', now())
                    ->orWhere('end_date', '<=', now()->addDays(10))
                    ->where('is_retired', false)
            )
            ->columns([
                TextColumn::make('number')
                    ->label('Numéro')
                    ->extraAttributes(['class' => 'font-bold']),

                ImageColumn::make('customer.logo')
                    ->label('Logo')
                    ->size(30)
                    ->rounded(),

                TextColumn::make('customer.name')
                    ->label('Client')
                    ->extraAttributes(['class' => 'font-bold']),

                TextColumn::make('is_active')
                    ->label('Statut')
                   ->badge()
                   ->getStateUsing(function ($record) {
                    $endDate = Carbon::parse($record->end_date);
                        if($endDate <= now()){
                            return 'Terminé';
                        } 
                        if (now()->diffInDays($record->end_date) <= 10) {
                            return "J-" . (int) now()->diffInDays($record->end_date);
                        }
                    })
                    ->colors([
                        'danger' => fn ($state): bool => $state === 'Terminé',
                        'info' => fn ($state): bool => str_starts_with($state, 'J-'),
                    ])
                    ->extraAttributes(['class' => 'font-bold']),
                ])->recordUrl(fn($record) => url('admin/devis/'.$record->id));
    }
}
