<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class DashboardPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';

    protected static ?string $navigationGroup = 'Dashboard';

    protected static ?string $title = 'Dashboard';

    protected static string $view = 'filament.pages.dashboard-page';
}
