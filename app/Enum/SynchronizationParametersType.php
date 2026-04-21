<?php

namespace App\Enum;

enum SynchronizationParametersType: string
{
    case GENERATOR    = 'generator';
    case DEVIS = 'devis';
    case CONSO_INTERNE    = 'conso_interne';
    case TECHNICIEN = 'technicien';
    case PIECE = 'piece';
    case CUSTOMER = 'customer';


    public function label(): string
    {
        return match ($this) {
            self::GENERATOR => 'Groupe Electrogène',
            self::DEVIS => 'Devis',
            self::CONSO_INTERNE => 'Conso Interne',
            self::TECHNICIEN => 'Technicien',
            self::PIECE => 'Piece',
            self::CUSTOMER => 'Client',
        };
    }
}
