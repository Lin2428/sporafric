<?php

namespace App\Filament\Pages;

use App\Filament\Admin\Pages\DailyReportPage;
use App\Filament\Utils\WidgetUtils;
use App\Models\Contract;
use App\Models\ReportMaintenance;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Collection;

class ReportMaintenancePage extends DailyReportPage implements HasForms
{
    use InteractsWithForms;
    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';
    protected static ?string $navigationGroup = 'Rapport';
    protected static ?string $title = 'Rapports des maintenances';

    protected static string $view = 'filament.pages.report-maintenance';

    private $data;

    protected function refresh(): void
    {
        if($this->contractId == null) {
            $data = null;
            return;
        }
        $contract = Contract::find($this->contractId);
        $query = ReportMaintenance::where('contract_id', $contract->id);

        $data = $query->get();

        $this->data = $data;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Select::make('contractId')
                // ->label('Contrat')
                // ->searchable()
                // ->options(Contract::all()->pluck('number', 'id'))
                // ->live()
               WidgetUtils::contractSelectWidget("contractId")
               ->hiddenLabel()
               ->afterStateUpdated(function ($state) {
                $this->contractId = $state;
                $this->refresh();
            })->live(true),
            ]);
    }

    protected function viewData(): array
    {
        return [
            'data' => $this->data,
        ];
    }
}
