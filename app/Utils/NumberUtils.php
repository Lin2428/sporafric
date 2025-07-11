<?php
namespace App\Utils;

use App\Models\Intervention;

class NumberUtils
{
    public static function format(int | float | null $number, int $decimals = 2, string $decimalSeparator = ',', bool $currency = false): string
    {
        if ($number == null) {
            return "0";
        }
        if (! ($number - floor($number) > 0)) {
            $decimals         = 0;
            $decimalSeparator = ' ';
        }

        $value = number_format($number, $decimals, $decimalSeparator, ' ');
        if (! $currency) {
            return $value;
        }

        return $value . ' FCFA';
    }

    public static function generate(int $length = 8): string
    {
        $x = '';
        for ($i = 1; $i <= $length; $i++) {
            $x .= dechex(random_int(0, 500) % 255);
        }

        return strtoupper(substr($x, 0, $length));
    }

    public static function intevention_numero(string $prefix): string
    {
        $last       = Intervention::latest()->first();

        $lastNumber = (int) $last?->numero;

        $parts = explode('-', $last?->numero);
        $lastPart = end($parts);

        $lastNumber = (int) $lastPart;

        $newNumber = $lastNumber + 1;

        $formattedNumber = str_pad($newNumber, 9, '0', STR_PAD_LEFT);

        return  $prefix.'-' . $formattedNumber;
    }

    public static function formatNumber(int $number, int $precision = 1): string
    {
        if ($number < 1000) {
            return (string) $number;
        }

        $suffixes  = ['K', 'M', 'G', 'T', 'P', 'E'];
        $index     = floor(log($number, 1000));
        $formatted = $number / pow(1000, $index);

        return round($formatted, $precision) . $suffixes[$index - 1];
    }
}
