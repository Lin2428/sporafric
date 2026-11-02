<?php

namespace App\Providers;

use App\Models\ContractGenerator;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Filament\Forms\Components\TextInput;
use Filament\Support\RawJs;
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

        // Champ montant FCFA : espaces des milliers pendant la saisie (12 500 000), nombre brut en base.
        // Décimale « . » pour relire les decimal(…,2) de la base (« 15000.00 ») sans les multiplier par 100.
        TextInput::macro('fcfa', function (bool $suffix = true) {
            /** @var TextInput $this */
            return $this
                ->mask(RawJs::make('$money($input, \'.\', \' \', 0)'))
                ->stripCharacters(' ')
                ->numeric()
                ->formatStateUsing(fn ($state) => $state === null || $state === '' ? null : (int) round((float) $state))
                ->suffix($suffix ? 'FCFA' : null);
        });

        Scramble::configure()
            ->withDocumentTransformers(function (OpenApi $openApi) {
                $openApi->secure(
                    SecurityScheme::http('bearer')
                );
            });
    }
}
