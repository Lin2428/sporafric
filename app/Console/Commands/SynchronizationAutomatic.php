<?php

namespace App\Console\Commands;

use App\Http\Controllers\OdooController;
use App\Models\User;
use Filament\Notifications\Notification;
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
        try {
            OdooController::syncronizeClient();
            OdooController::syncronizeTechnicians();
            OdooController::syncronizePieces();
            OdooController::syncronizeGenerator(all: true);
            OdooController::syncronizeDevis(all: true);
            OdooController::syncronizeConsoInterne();
            Log::info('Synchronization éxecutée à ' . now());

            Notification::make()
                ->title("Synchronisation automatique")
                ->body("Synchronisation automatique effectuée avec succès")
                ->success()
                ->icon('heroicon-o-arrow-path-rounded-square')
                ->sendToDatabase($this->superReceiver());
        } catch (\Throwable $th) {
            //$this->error($th->getMessage());
            Notification::make()
                ->title("Synchronisation automatique")
                ->body("Synchronisation automatique échouée")
                ->success()
                ->icon('heroicon-o-arrow-path-rounded-square')
                ->sendToDatabase($this->superReceiver());
        }
    }

    public static function superReceiver(): mixed
    {
        return  User::role(['super_admin', 'Superviseur'])->get();
    }
}
