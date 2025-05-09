<?php

namespace App\Enum;

enum FactureType: string
{
    case SOUS_CONTRAT    = '0';
    case HORS_CONTRAT_1 = '1';
    case HORS_CONTRAT_2    = '2';

    public function label(): string
    {
        return match ($this) {
            self::SOUS_CONTRAT => 'Pris en charge dans le contrat',
            self::HORS_CONTRAT_1 => 'Hors contrat, Facturable après intervention',
            self::HORS_CONTRAT_2 => 'Hors contrat, Facturable avant intervention',
        };
    }
}
