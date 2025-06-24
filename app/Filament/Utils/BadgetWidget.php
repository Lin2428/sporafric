<?php

namespace App\Filament\Utils;

use Closure;
use Filament\Tables\Columns\TextColumn;

class BadgetWidget
{
    public static function boleanToBadget(bool|null $bool, string $label1, string $label2): string
    {
        $label = $bool ? $label1 : $label2;
        $color = $bool ? 'bg-green-500' : 'bg-red-500';

        return "<span class='{$color} text-white font-bold text-medium px-4 py-1 rounded-full'>{$label}</span>";
    }

    public static function generatorStatusBadget(string $status): string
    {
        $color = match ($status) {
            'Disponible' => 'bg-green-500',
            'En révision' => 'bg-yellow-500',
            'En location' => 'bg-blue-500',
            'Indisponible ' => 'bg-red-500',
            default => 'bg-blue-500',
        };

        return "<span class='{$color} text-white font-bold text-medium px-4 py-1 rounded-full'>{$status}</span>";
    }

    public static function interventionStatusBadget(string $status): string
    {
        $color = match ($status) {
            'En cours' => 'bg-yellow-500',
            'Terminée' => 'bg-green-500',
            'Planifiée' => 'bg-blue-500',
            default => 'bg-blue-500',
        };

        return "<span class='{$color} text-white font-bold text-medium px-4 py-1 rounded-full'>{$status}</span>";
    }

    public static function devisState(string $status): string
    {
        $color = match ($status) {
            'Envoyé' => 'bg-purple-500',
            'Verrouillé' => 'bg-green-500',
            'Devis' => 'bg-blue-500',
            'Bon de commande' => 'bg-yellow-500',
            'Annulé' => 'bg-red-500',
            default => 'bg-blue-500',
        };

        return "<span class='{$color} text-white font-bold text-medium px-4 py-1 rounded-full'>{$status}</span>";
    }
}
