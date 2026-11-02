<?php

namespace App\Enum;

enum GeneratorStatus: string
{
    case DISPONIBLE = '0';
    case EN_REVU = '1';
    case EN_LOCATION = '2';
    case INDISPONIBLE = '3';
    case EN_PRET = '4';

    public function label(): string
    {
        return match ($this) {
            self::DISPONIBLE => 'Disponible',
            self::EN_REVU => 'En révision',
            self::EN_LOCATION => 'En location',
            self::INDISPONIBLE => 'Indisponible',
            self::EN_PRET => 'En prêt',
        };
    }

    /** Couleurs de badge (fond clair, texte foncé). */
    public function colors(): array
    {
        return match ($this) {
            self::DISPONIBLE   => ['#dcfce7', '#166534'], // vert
            self::EN_LOCATION  => ['#dbeafe', '#1e40af'], // bleu
            self::EN_PRET      => ['#ede9fe', '#5b21b6'], // violet
            self::EN_REVU      => ['#fef3c7', '#92400e'], // orange
            self::INDISPONIBLE => ['#fee2e2', '#991b1b'], // rouge
        };
    }
}
