<?php

namespace App\Enum;

enum InterventionType: string
{
    case DEPANNAGE    = '0';
    case INSTALLATION = '1';
    case MAINTENANCE_PRE    = '2';
    case MAINTENANCE_CUR   = '3';
    case RETRAIT = '4';
    case VIDANGE = '5';
    case REMPLACEMENT = '6';

    public function label(): string
    {
        return match ($this) {
            self::DEPANNAGE => 'Dépannage',
            self::INSTALLATION => 'Installation',
            self::MAINTENANCE_PRE => 'Maintenance préventive',
            self::MAINTENANCE_CUR => 'Maintenance curative',
            self::RETRAIT => 'Retrait',
            self::VIDANGE => 'Vidange',
            self::REMPLACEMENT => 'Remplacement',
        };
    }

    /** Couleur du type (planning : liseré + pastille). */
    public function color(): string
    {
        return match ($this) {
            self::DEPANNAGE       => '#ef4444', // rouge
            self::INSTALLATION    => '#3b82f6', // bleu
            self::MAINTENANCE_PRE => '#22c55e', // vert
            self::MAINTENANCE_CUR => '#64748b', // ardoise
            self::RETRAIT         => '#f97316', // orange
            self::VIDANGE         => '#eab308', // jaune
            self::REMPLACEMENT    => '#8b5cf6', // violet
        };
    }
}
