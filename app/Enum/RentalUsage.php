<?php

namespace App\Enum;

/**
 * Type d'utilisation d'un GE de location sur une journée (planning du rapport de location).
 * Déduit du client de la commande Odoo (clients dédiés PRÊT / MAINTENANCE / DOTATIONS)
 * et des interventions pour la maintenance.
 */
enum RentalUsage: string
{
    case LOCATION = 'L';
    case PRET = 'P';
    case MAINTENANCE = 'M';
    case DOTATION = 'D';

    public function label(): string
    {
        return match ($this) {
            self::LOCATION    => 'Location GE',
            self::PRET        => 'Prêt GE',
            self::MAINTENANCE => 'Maintenance GE',
            self::DOTATION    => 'Dotations – relations publiques GE',
        };
    }

    /** Couleurs du planning (fond, texte). */
    public function colors(): array
    {
        return match ($this) {
            self::LOCATION    => ['#16a34a', '#ffffff'],
            self::PRET        => ['#2563eb', '#ffffff'],
            self::MAINTENANCE => ['#dc2626', '#ffffff'],
            self::DOTATION    => ['#9333ea', '#ffffff'],
        };
    }

    /** Priorité quand plusieurs usages tombent le même jour (le plus fort gagne). */
    public function priority(): int
    {
        return match ($this) {
            self::LOCATION    => 4,
            self::PRET        => 3,
            self::DOTATION    => 2,
            self::MAINTENANCE => 1,
        };
    }
}
