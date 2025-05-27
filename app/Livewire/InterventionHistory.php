<?php

namespace App\Livewire;

use App\Models\ReportMaintenance;
use Livewire\Component;

class InterventionHistory extends Component
{
    public $generatorId;
    public $data = [];

    public function mount($generatorId)
    {
        $this->generatorId = $generatorId;

        $this->data = ReportMaintenance::query()
            ->where('generator_id', $this->generatorId)
            ->get();
    }

    public function render()
    {
        return view('livewire.intervention-history');
    }
}
