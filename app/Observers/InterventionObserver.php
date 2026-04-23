<?php

namespace App\Observers;

use App\Enum\InterventionType;
use App\Models\Intervention;
use App\Models\User;
use App\Utils\DateUtils;
use Filament\Notifications\Notification;

class InterventionObserver
{
    /**
     * Handle the Intervention "created" event.
     */


    public function created(Intervention $intervention): void
    {
        Notification::make()
            ->title("Nouvelle intervention")
            ->body("Une nouvelle intervention de type ".
                mb_strtolower(InterventionType::from($intervention->type)->label()).
                " à été créer au numéro $intervention->numero par ".
                auth()->user()->name
            )
            ->success()
            ->icon('heroicon-o-wrench-screwdriver')
            ->sendToDatabase($this->superReceiver());
    }

    /**
     * Handle the Intervention "updated" event.
     */
    public function updated(Intervention $intervention): void
    {

        if($intervention->getOriginal('date_planifiee') != $intervention->date_planifiee)
        {
            Notification::make()
                ->title("Planification de l'intervention")
                ->body("L'intervention $intervention->numero de type ".
                    mb_strtolower(InterventionType::from($intervention->type)->label()).
                    " à été planifiée pour ". DateUtils::calendar($intervention->getOriginal('date_planifiee')).
                    " par ".auth()->user()->name
                )
                ->info()
                ->icon('heroicon-o-wrench-screwdriver')
                ->sendToDatabase($this->superReceiver());
            return;
        }

        if($intervention->getOriginal('generator_id') != $intervention->generator_id)
        {
            Notification::make()
                ->title("Intervention mise à jour")
                ->body("Le GE de l'intervention $intervention->numero a été modifiée par ". auth()->user()->name)
                ->info()
                ->icon('heroicon-o-wrench-screwdriver')
                ->sendToDatabase($this->superReceiver());
            return;
        }

        Notification::make()
            ->title("Intervention mis à jour")
            ->body("L'intervention $intervention->numero a été mise à jour par ". auth()->user()->name)
            ->icon('heroicon-o-wrench-screwdriver')
            ->sendToDatabase($this->superReceiver());
    }

    /**
     * Handle the Intervention "deleted" event.
     */
    public function deleted(Intervention $intervention): void
    {
        Notification::make()
            ->title("Intervention supprimée")
            ->body(body: "L'intervention $intervention->numero a été supprimée  par ". auth()->user()->name)
            ->icon('heroicon-o-wrench-screwdriver')
            ->sendToDatabase($this->superReceiver());
    }

    /**
     * Handle the Intervention "restored" event.
     */
    public function restored(Intervention $intervention): void
    {
        //
    }

    /**
     * Handle the Intervention "force deleted" event.
     */
    public function forceDeleted(Intervention $intervention): void
    {
        //
    }

    public static function superReceiver(): mixed
    {
         return  User::role(['super_admin', 'Superviseur'])->get();
    }
}
