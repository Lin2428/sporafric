<?php

namespace App\Observers;

use App\Models\Devis;
use App\Models\User;
use Filament\Notifications\Notification;

class DevisObserver
{
    /**
     * Handle the Devis "created" event.
     */
    public function created(Devis $devis): void
    {
       //
    }

    /**
     * Handle the Devis "updated" event.
     */
    public function updated(Devis $devis): void
    {
        Notification::make()
            ->title("Devis mis à jour")
            ->body("Le devis $devis->number a été mis à jour  par ".
                auth()->user()->name
            )
            ->icon('heroicon-o-clipboard-document-list')
            ->sendToDatabase($this->superReceiver());
    }

    /**
     * Handle the Devis "deleted" event.
     */
    public function deleted(Devis $devis): void
    {
        Notification::make()
            ->title("Devis supprimé")
            ->body("Le devis $devis->number a été supprimé  par ".
                auth()->user()->name
            )
            ->danger()
            ->icon('heroicon-o-clipboard-document-list')
            ->sendToDatabase($this->superReceiver());
    }

    /**
     * Handle the Devis "restored" event.
     */
    public function restored(Devis $devis): void
    {
        //
    }

    /**
     * Handle the Devis "force deleted" event.
     */
    public function forceDeleted(Devis $devis): void
    {
        //
    }

    public static function superReceiver(): mixed
    {
        return  User::role(['super_admin', 'Superviseur', 'Secrétaire'])->get();
    }
}
