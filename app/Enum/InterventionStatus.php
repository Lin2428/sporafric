<?php

namespace App\Enum;

enum InterventionStatus: string
{
    case PLANIFIEE = '0';
    case EN_COURS = '1';
    case TERMINEE = '2';

    public function label(): string
    {
        return match ($this) {
            self::PLANIFIEE => 'planifiée',
            self::EN_COURS => 'En cours',
            self::TERMINEE => 'Terminée',
        };
    }
}
