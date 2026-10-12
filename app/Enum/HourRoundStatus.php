<?php

namespace App\Enum;

enum HourRoundStatus: string
{
    case PLANIFIEE = '0';
    case EN_COURS = '1';
    case TERMINEE = '2';

    public function label(): string
    {
        return match ($this) {
            self::PLANIFIEE => 'Planifiée',
            self::EN_COURS => 'En cours',
            self::TERMINEE => 'Terminée',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PLANIFIEE => 'info',
            self::EN_COURS => 'warning',
            self::TERMINEE => 'success',
        };
    }
}
