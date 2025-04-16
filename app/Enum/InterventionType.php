<?php
namespace App\Enum;

enum InterventionType: string {
    case DEPANNAGE    = '0';
    case INSTALLATION = '1';
    case MAINTENANCE_PRE    = '2';
    case MAINTENANCE_CUR   = '3';

    public function label(): string
    {
        return match ($this) {
            self::DEPANNAGE => 'Dépannage',
            self::INSTALLATION => 'Installation',
            self::MAINTENANCE_PRE => 'Maintenance préventive',
            self::MAINTENANCE_CUR => 'Maintenance curative',
        };
    }
}
