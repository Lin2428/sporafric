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

class ReportMaintenancePage extends DailyReportPage implements HasForms
{
    use InteractsWithForms;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Rapport';
    protected static ?string $title = 'Rapports de maintenance';

    protected static string $view = 'filament.pages.report-maintenance';

    private $data;
    public $type;

    protected function refresh(): void
    {
        if (empty($this->contractId) && empty($this->generatorId)) {
            $this->data = collect();
            return;
        }
        
        
        $query = ReportMaintenance::query();
        
        if (!empty($this->contractId)) {
            $query->where('contract_id', $this->contractId);
        }
        
        if (!empty($this->generatorId)) {
            $query->orWhere('generator_id', $this->generatorId);
        }
        
        $this->data = $query->get();
       
    }

    public function form(Form $form): Form
{
    return $form
        ->schema([

            WidgetUtils::contractSelectWidget("contractId")
                ->afterStateUpdated(function ($state) {
                    $this->generatorId = null;
                    $this->contractId = $state;
                    $this->refresh();
                })
                ->live(true)
                ->required(false)
                ->visible(fn(callable $get) => $get('type') == '0'),

            WidgetUtils::generatorSelectWidget(name:"generatorId", isDispo:false)
                ->afterStateUpdated(function ($state) {
                    $this->contractId = null;
                    $this->generatorId = $state;
                    $this->refresh();
                })
                ->live(true)
                ->required(false)
                ->visible(fn(callable $get) => $get('type') == '1'),

                Select::make('type')
                ->label('Type de rapport')
                ->options([
                    '0' => 'Contrat',
                    '1' => 'Groupe électrogène',
                ])
                ->reactive()
                ->extraAttributes(['class' => 'no-print']),
        ])
        ->columns(2);
}

  

    protected function viewData(): array
    {
        return [
            'data' => $this->data,
        ];
    }
}
