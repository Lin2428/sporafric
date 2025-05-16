<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class ReportLocation extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Rapport';
    protected static ?string $title = 'Rapports des locations';
    protected static string $view = 'filament.pages.report-location';
}
