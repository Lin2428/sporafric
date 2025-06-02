<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\CalendarView;
use Filament\Pages\Page;

class CanlendarPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
     protected static ?string $title = 'Planning des interventions';
    protected static ?string $navigationGroup = 'Dashboard';
    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.pages.canlendar-page';

    public function getHeaderWidgets(): array
    {
        return [
            CalendarView::class,
        ];
    }
}
