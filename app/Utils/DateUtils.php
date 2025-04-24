<?php

namespace App\Utils;

class DateUtils
{
    public static function format($date): string
    {
        return date('d/m/Y', strtotime($date));
    }
}
