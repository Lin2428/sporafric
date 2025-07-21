<?php

namespace App\Filament\Admin\Pages;
use App\Models\Contract;
use App\Models\Devis;
use App\Models\Generator;
use Filament\Pages\Page;
abstract class DailyReportPage extends Page
{
    public  $contractId;

    public $devisId;

    public $generatorId;

    public $startDate;
    public $endDate;
    public $selectDateRange;

    public function mount(): void
    {
        $this->refresh();
    }

    public function updatedDate(): void
    {
        $this->refresh();
    }

    public function updatedContractId(): void
    {
        $this->refresh();
    }

    protected abstract function refresh();

    protected abstract function viewData(): array;

    protected function getViewData(): array
    {
        return [
            'contractId' => $this->contractId,
            'contracts' => Contract::all(),
            'devisId' => $this->devisId,
            'devis' => Devis::all(),
            'generator' => $this->generatorId,
            "generators" => Generator::all(),
            "startDate" => $this->startDate,
            "endDate" => $this->endDate,
            'selectDateRange' => $this->selectDateRange,
            ...$this->viewData(),
        ];
    }
}
