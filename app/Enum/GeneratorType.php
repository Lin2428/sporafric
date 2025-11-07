<?php

namespace App\Enum;

enum GeneratorType: string
{
    case LOCATION = '1';
    case MAINTENANCE = '2';

    public function label(): string
    {
        return match ($this) {
            self::LOCATION => 'Location',
            self::MAINTENANCE => 'Maintenance',
        };
    }
}
