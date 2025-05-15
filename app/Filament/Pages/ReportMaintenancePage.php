<?php

namespace App\Filament\Pages;

use App\Filament\Admin\Pages\DailyReportPage;
use App\Models\Contract;
use App\Models\ReportMaintenance;
use Filament\Pages\Page;

class ReportMaintenancePage extends DailyReportPage
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';
    protected static ?string $navigationGroup = 'Rapport';
    protected static ?string $title = 'Rapports des maintenances';

    protected static string $view = 'filament.pages.report-maintenance';

    private mixed $data = [];

    protected function refresh(): void
    {
        $contract = Contract::find($this->contractId);
        $query = ReportMaintenance::where('contract_id', $contract->id);

        $data = $query->get();

        $this->data = $data;
    }

    protected function viewData(): array
    {
        return [
            'data' => $this->data,
        ];
    }
}
