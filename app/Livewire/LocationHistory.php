<?php

namespace App\Livewire;

use App\Models\DevisGenerator;
use Livewire\Component;

class LocationHistory extends Component
{
    public $generatorId;
    public $data = [];
    public $model = "";

    
    public function mount($generatorId, $model)
    {
        $this->generatorId = $generatorId;
        $this->model = $model;
       
            $this->data = DevisGenerator::where('devis_id', '<>', null)
            ->where('generator_id', $this->generatorId)
            ->orWhere('old_generator_id', $this->generatorId)
            ->distinct('devis_id')
            ->with('devis')
            ->get();
       
        
    }

    public function render()
    {
        return view('livewire.location-history');
    }
}
