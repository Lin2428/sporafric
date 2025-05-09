<?php

namespace App\Enum;

enum InterventionStatus: string
{
    case NON_COMMENCE = '0';
    case EN_COURS = '1';
    case TERMINEE = '3';
    case ANNULEE = '4';

    public function label(): string
    {
        return match ($this) {
            self::NON_COMMENCE => 'Non commencée',
            self::EN_COURS => 'En cours',
            self::TERMINEE => 'Terminée',
            self::ANNULEE => 'Annulée',
        };
    }
}
