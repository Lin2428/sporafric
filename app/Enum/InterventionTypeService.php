<?php

namespace App\Enum;

enum InterventionTypeService: string
{
    case LOCATION    = '0';
    case MAINTENANCE = '1';
    case CONSO_INTERNE    = '2';

    public function label(): string
    {
        return match ($this) {
            self::LOCATION => 'Location',
            self::MAINTENANCE => 'Maintenance',
            self::CONSO_INTERNE => 'Conso Interne',
        };
    }
}
