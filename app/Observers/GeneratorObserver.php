<?php

namespace App\Observers;

use App\Enum\GeneratorStatus;
use App\Enum\GeneratorType;
use App\Models\Generator;
use App\Models\User;
use Filament\Notifications\Notification;

class GeneratorObserver
{
    /**
     * Handle the Generator "created" event.
     */
    public function created(Generator $generator): void
    {
        if($generator->type == GeneratorType::MAINTENANCE->value){
            Notification::make()
                ->title("Nouveau GE")
                ->body("Un nouveau GE de maintenance a été créer  avec  l'identification  $generator->name par ".
                    auth()->user()->name
                )
                ->success()
                ->icon('icon-generator')
                ->sendToDatabase($this->superReceiver());
        }
    }

    /**
     * Handle the Generator "updated" event.
     */
    public function updated(Generator $generator): void
    {
       if(($generator->getOriginal('status') != $generator->status) && ($generator->type == GeneratorType::LOCATION->value))
       {
           Notification::make()
               ->title("Statut du GE changé")
               ->body("Le GE $generator->name est passé au statut ". GeneratorStatus::from($generator->status)->label())
               ->info()
               ->icon('icon-generator')
               ->sendToDatabase($this->superReceiver());
           return;
       }

        Notification::make()
            ->title("GE mis à jour")
            ->body("Le GE $generator->name a été mis à jour par ".auth()->user()->name)
            ->icon('icon-generator')
            ->sendToDatabase($this->superReceiver());
    }

    /**
     * Handle the Generator "deleted" event.
     */
    public function deleted(Generator $generator): void
    {
        Notification::make()
            ->title("GE Supprimé")
            ->body("Le GE $generator->name a été supprimé par ".auth()->user()->name)
            ->danger()
            ->icon('icon-generator')
            ->sendToDatabase($this->superReceiver());
    }

    /**
     * Handle the Generator "restored" event.
     */
    public function restored(Generator $generator): void
    {
        //
    }

    /**
     * Handle the Generator "force deleted" event.
     */
    public function forceDeleted(Generator $generator): void
    {
        //
    }

    public static function superReceiver(): mixed
    {
        return  User::role(['super_admin', 'Superviseur'])->get();
    }
}
