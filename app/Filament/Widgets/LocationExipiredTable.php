<?php

namespace App\Filament\Widgets;

use App\Models\Devis;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LocationExipiredTable extends BaseWidget
{
    protected static ?string $heading = 'Locations Terminées';
    public function table(Table $table): Table
    {
        return $table
            ->query(
               Devis::query()
            )
            ->columns([
                
            ]);
    }
}
