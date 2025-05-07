<?php

namespace App\Enum;

enum HoraireType: string
{
    case NORMAL    = '0';
    case ASTRINTE = '1';

    public function label(): string
    {
        return match ($this) {
            self::NORMAL => 'Journée normale',
            self::ASTRINTE => 'Astrinte',
        };
    }
}
