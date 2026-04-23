<?php

namespace App\Providers;

use App\Models\ContractGenerator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \App\Models\Contract::observe(\App\Observers\ContractObserver::class);
        \App\Models\ContractGenerator::Observe(\App\Observers\ContractObserver::class);
        \App\Models\Devis::observe(\App\Observers\DevisObserver::class);
        \App\Models\Generator::observe(\App\Observers\GeneratorObserver::class);
        \App\Models\Intervention::observe(\App\Observers\InterventionObserver::class);
    }
}
