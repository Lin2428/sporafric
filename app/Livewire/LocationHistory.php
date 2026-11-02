<?php

namespace App\Livewire;

use App\Models\GeneratorStatusLog;
use App\Models\InternalConsumption;
use App\Models\RentalPeriod;
use App\Services\RentalReportService;
use Livewire\Component;

class LocationHistory extends Component
{
    public $generatorId;
    public $model = "";

    public function mount($generatorId, $model)
    {
        $this->generatorId = $generatorId;
        $this->model = $model;
    }

    public function render()
    {
        // Périodes importées d'Odoo : dates réelles de départ / retour (mouvements de location)
        $periods = RentalPeriod::query()
            ->with('customer:id,name,contact_c_phone')
            ->where('generator_id', $this->generatorId)
            ->whereNotIn('order_state', ['cancel'])
            ->orderByDesc('planned_start')
            ->get();

        return view('livewire.location-history', [
            'data'           => $periods,
            'usageCustomers' => RentalReportService::usageCustomers(),
            'loans'          => RentalReportService::replacementLoans([(int) $this->generatorId])
                ->map(fn ($l) => $l + [
                    'role'  => $l['loaned_id'] === (int) $this->generatorId ? 'loaned' : 'replaced',
                    'other' => \App\Models\Generator::query()->without(['contractGenerator', 'etat', 'files'])
                        ->find($l['loaned_id'] === (int) $this->generatorId ? $l['replaced_id'] : $l['loaned_id'])?->reference,
                ])
                ->sortByDesc('start'),
            'consumptions'   => InternalConsumption::query()->where('generator_id', $this->generatorId)->whereNot('order_state', 'cancel')->latest('order_date')->get(),
            'statusLogs'     => GeneratorStatusLog::query()->with('user:id,name')->where('generator_id', $this->generatorId)->latest()->limit(50)->get(),
        ]);
    }
}
