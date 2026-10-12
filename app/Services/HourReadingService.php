<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Generator;
use App\Models\HourReading;
use App\Models\HourRound;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Relevés d'heures de fonctionnement : historisation + report sur le groupe
 * (même règle que la page « Vidanges » : next_vidange = prochain_visite - houres,
 * vidange = next_vidange > 30).
 */
class HourReadingService
{
    /**
     * Groupes électrogènes rattachés (contrat actif ou devis en cours) aux clients donnés.
     */
    public static function generatorsForCustomers(array $customerIds): Collection
    {
        if (empty($customerIds)) {
            return collect();
        }

        return Generator::query()
            ->without(['etat', 'files'])
            ->with(['contractGenerator.contract.customer', 'devisGenerator.devis.customer'])
            ->where(function ($query) use ($customerIds) {
                $query->whereHas('contractGenerator.contract', fn ($q) => $q->whereIn('customer_id', $customerIds))
                    ->orWhereHas('devisGenerator.devis', fn ($q) => $q->whereIn('customer_id', $customerIds));
            })
            ->orderBy('name')
            ->get();
    }

    /**
     * Libellé d'un groupe pour les sélecteurs : « Client — Site — GE (kVA) ».
     */
    public static function generatorLabel(Generator $generator): string
    {
        $customer = static::customerOf($generator);
        $site     = $generator->contractGenerator?->site ?? $generator->devisGenerator?->site;

        return collect([$customer?->name, $site, $generator->name . ($generator->power ? " ({$generator->power} kVA)" : '')])
            ->filter()
            ->implode(' — ');
    }

    public static function customerOf(Generator $generator): ?Customer
    {
        return $generator->contractGenerator?->contract?->customer
            ?? $generator->devisGenerator?->devis?->customer;
    }

    /**
     * Ajoute des groupes (lignes à relever) à une tournée, sans doublon.
     */
    public static function addGenerators(HourRound $round, array $generatorIds): void
    {
        $existing = $round->readings()->pluck('generator_id')->all();
        $position = (int) $round->readings()->max('position');

        $generators = Generator::query()
            ->without(['etat', 'files'])
            ->with(['contractGenerator.contract', 'devisGenerator.devis'])
            ->whereIn('id', array_diff($generatorIds, $existing))
            ->get()
            ->sortBy(fn (Generator $g) => [static::customerOf($g)?->name, $g->contractGenerator?->site ?? $g->devisGenerator?->site]);

        foreach ($generators as $generator) {
            $round->readings()->create([
                'generator_id' => $generator->id,
                'customer_id'  => $generator->contractGenerator?->contract?->customer_id ?? $generator->devisGenerator?->devis?->customer_id,
                'site'         => $generator->contractGenerator?->site ?? $generator->devisGenerator?->site,
                'code_site'    => $generator->contractGenerator?->code_site ?? $generator->devisGenerator?->code_site,
                'position'     => ++$position,
            ]);
        }

        $round->refreshStatus();
    }

    /**
     * Enregistre la valeur relevée sur une ligne (création ou correction) et la
     * reporte sur le groupe si c'est le relevé le plus récent de ce groupe.
     */
    public static function record(HourReading $reading, int $houres, ?Carbon $readAt = null, ?string $note = null, ?int $userId = null): HourReading
    {
        $generator = $reading->generator()->without(['etat', 'files'])->first();
        $readAt  ??= now();

        // L'état « avant relevé » n'est figé qu'à la première saisie (une correction le garde).
        if ($reading->houres === null) {
            $reading->previous_houres = is_numeric($generator?->houres) ? (int) $generator->houres : null;
        }

        $prochainVisite = $generator?->prochain_visite;

        $reading->fill([
            'houres'          => $houres,
            'prochain_visite' => $prochainVisite,
            'next_vidange'    => $prochainVisite !== null ? $prochainVisite - $houres : null,
            'read_at'         => $readAt,
            'note'            => $note,
            'user_id'         => $userId ?? $reading->user_id,
        ])->save();

        if ($generator && static::isLatest($reading)) {
            $generator->houres = $houres;
            if ($prochainVisite !== null) {
                $generator->next_vidange = $prochainVisite - $houres;
                $generator->vidange      = $generator->next_vidange > 30;
            }
            // Pas de notification « GE mis à jour » par ligne de tournée.
            $generator->saveQuietly();
        }

        $reading->round?->refreshStatus();

        return $reading;
    }

    /**
     * Saisie directe au bureau (page « Vidanges ») : relevé hors tournée.
     */
    public static function recordManual(Generator $generator, int $houres): HourReading
    {
        $reading = new HourReading([
            'generator_id' => $generator->id,
            'customer_id'  => static::customerOf($generator)?->id,
            'site'         => $generator->contractGenerator?->site ?? $generator->devisGenerator?->site,
            'code_site'    => $generator->contractGenerator?->code_site ?? $generator->devisGenerator?->code_site,
        ]);

        return static::record($reading, $houres, now(), null, auth()->id());
    }

    protected static function isLatest(HourReading $reading): bool
    {
        return ! HourReading::query()
            ->where('generator_id', $reading->generator_id)
            ->where('id', '<>', $reading->id)
            ->where('read_at', '>', $reading->read_at)
            ->exists();
    }
}
