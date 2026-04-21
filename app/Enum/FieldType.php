<?php

namespace App\Enum;

enum FieldType: string
{
    case INT    = 'int';
    case FLOAT = 'float';
    case STRING = 'string';
    case DATE = 'date';
    case DATETIME = 'datetime';
    case BOOLEAN = 'boolean';


    public function label(): string
    {
        return match ($this) {
            self::INT => 'Entier',
            self::FLOAT => 'Flottant',
            self::STRING => 'Chaîne de caractères',
            self::DATE => 'Date',
            self::DATETIME => 'Date et heure',
            self::BOOLEAN => 'Booléen',
        };
    }
}
