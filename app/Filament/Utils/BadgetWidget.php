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
      
        return "<span class='{$color} text-black text-medium px-4 py-1 rounded-full'>{$label}</span>";
          
    }
}
