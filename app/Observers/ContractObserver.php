<?php

namespace App\Observers;

use App\Enum\InterventionType;
use App\Models\Contract;
use App\Models\User;
use Filament\Notifications\Notification;

class ContractObserver
{
    /**
     * Handle the Contract "created" event.
     */
    public function created(Contract $contract): void
    {
        Notification::make()
            ->title("Nouveau contrat")
            ->body("Un nouveau contrat a été créer pour le client ". $contract->customer->name  ." au numéro $contract->number par ".
                auth()->user()->name
            )
            ->success()
            ->icon('heroicon-o-clipboard-document')
            ->sendToDatabase($this->superReceiver());
    }

    /**
     * Handle the Contract "updated" event.
     */
    public function updated(Contract $contract): void
    {
        Notification::make()
            ->title("Contrat mis à jour")
            ->body("Le contrat $contract->number a été mis à jour  par ".
                auth()->user()->name
            )
            ->icon('heroicon-o-clipboard-document')
            ->sendToDatabase($this->superReceiver());
    }

    /**
     * Handle the Contract "deleted" event.
     */
    public function deleted(Contract $contract): void
    {
        Notification::make()
            ->title("Contrat supprimé")
            ->body("Le contrat $contract->number a été supprimé  par ".
                auth()->user()->name
            )
            ->danger()
            ->icon('heroicon-o-clipboard-document')
            ->sendToDatabase($this->superReceiver());
    }

    /**
     * Handle the Contract "restored" event.
     */
    public function restored(Contract $contract): void
    {
        //
    }

    /**
     * Handle the Contract "force deleted" event.
     */
    public function forceDeleted(Contract $contract): void
    {
        //
    }

    public static function superReceiver(): mixed
    {
        return  User::role(['super_admin', 'Superviseur', 'Secrétaire'])->get();
    }
}
