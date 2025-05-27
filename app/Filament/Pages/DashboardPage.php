<?php

namespace App\Filament\Pages;

use App\Enum\InterventionStatus;
use App\Filament\Widgets\GeneratorStats;
use App\Filament\Widgets\TextWidget;
use App\Models\Intervention;
use Filament\Pages\Page;

class DashboardPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';

    protected static ?string $navigationGroup = 'Dashboard';

    protected static ?string $title = 'Dashboard';
    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.dashboard-page';

    public $interventionsDuJour = [];

    public function mount()
    {
        $this->interventionsDuJour = Intervention::whereBetween('date_planifiee', [now()->subWeek(), now()->addDay()])
        ->where('status', '!=', InterventionStatus::ANNULEE->value)
        ->where('status', '!=', InterventionStatus::TERMINEE->value)
        ->with(['interventionTechniciens', 'pieces', 'contract', 'customer'])
        ->orderBy('date_planifiee', 'asc')
        ->get();
    }
}
