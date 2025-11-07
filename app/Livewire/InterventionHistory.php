<?php

namespace App\Livewire;

use App\Models\ReportLocation;
use App\Models\ReportMaintenance;
use Livewire\Component;

class InterventionHistory extends Component
{
    public $generatorId;
    public $data = [];
    public $model = "";

    public function mount($generatorId, $model)
    {
        $this->generatorId = $generatorId;
        $this->model = $model;

        if ($model == ReportMaintenance::class) {
            $this->data = ReportMaintenance::query()
                ->where('generator_id', $this->generatorId)
                ->orWhere('new_generator_id', $this->generatorId)
                ->get();
        } else {
            $this->data = ReportLocation::query()
                ->where('generator_id', $this->generatorId)
                ->orWhere('new_generator_id', $this->generatorId)
                ->get();
        }
    }

    public function render()
    {
        return view('livewire.intervention-history');
    }
}
