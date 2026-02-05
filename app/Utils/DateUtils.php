<?php

namespace App\Utils;

class DateUtils
{
    public static function format($date): string
    {
        return date('d/m/Y', strtotime($date));
    }

    public static function formatWithTime($date): string
    {
        return date('d/m/Y H:i', strtotime($date));
    }

    public static function formatForReport($date): string
    {
        return date('d/m H:i', strtotime($date));
    }
}
