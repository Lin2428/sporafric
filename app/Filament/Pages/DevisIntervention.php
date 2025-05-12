<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class DevisIntervention extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationGroup = 'Location';
    protected static ?string $navigationLabel = 'Interventions';
    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.devis-intervention';
}
