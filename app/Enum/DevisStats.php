<?php

namespace App\Enum;

enum DevisStats: string
{
    case DEVIS = 'draft';
    case ENVOYE = 'sent';
    case COMMANDE = 'sale';
    case DONE = 'done';
    case CANCEL = 'cancel';

    public function label(): string
    {
        return match ($this) {
            self::DEVIS => 'Devis',
            self::ENVOYE => 'Envoyé',
            self::COMMANDE => 'Bon de commande',
            self::DONE => 'Verrouillé',
            self::CANCEL => 'Annulé',
        };
    }


}