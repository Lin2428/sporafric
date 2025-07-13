<?php

namespace App\Console\Commands;

use App\Http\Controllers\OdooController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SynchronizationAutomatic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:synchronization-automatic';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Syncronization autmatique des éléments à recuperer sur Odoo';

    /**
     * Execute the console command.
     */
    public function handle()
    {
            set_time_limit(500);

            OdooController::syncronizeClient();
            OdooController::syncronizeTechnicians();
            OdooController::syncronizePieces();
            OdooController::syncronizeGenerator(all: true);
            OdooController::syncronizeDevis(all: true);
            Log::info('Synchronization éxecutée à ' . now());
        try {
            
        } catch (\Throwable $th) {
            $this->error($th->getMessage());
        }
    }
}
