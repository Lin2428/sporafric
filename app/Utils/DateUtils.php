<?php

namespace App\Utils;

use Carbon\Carbon;

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

    public static function calendar($date): string
    {
        $date = Carbon::parse($date);
        return $date->calendar();
    }
}
