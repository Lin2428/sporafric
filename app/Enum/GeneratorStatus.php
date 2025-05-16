<?php
namespace App\Enum;

enum GeneratorStatus: string
{
    case DISPONIBLE = '0';
    case EN_REVU = '1';
    case EN_LOCATION = '2';
    case INDISPONIBLE = '3';

    public function label(): string
    {
        return match ($this) {
            self::DISPONIBLE => 'Disponible',
            self::EN_REVU => 'En révision',
            self::EN_LOCATION => 'En location',
            self::INDISPONIBLE => 'Indisponible',
        };
    }
}