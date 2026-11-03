<?php

namespace App\Support;

class Money
{
    public static function pkr(float|int|string|null $amount): string
    {
        $value = (float) $amount;
        $decimals = abs($value - round($value)) < 0.001 ? 0 : 2;

        return 'Rs '.number_format($value, $decimals);
    }

    public static function percent(float|int|string|null $value): string
    {
        $number = (float) $value;
        $decimals = abs($number - round($number)) < 0.001 ? 0 : 1;

        return number_format($number, $decimals).'%';
    }

    public static function units(string $unit, int $count): string
    {
        if ($count === 1) {
            return $unit;
        }

        return $unit === 'box' ? 'boxes' : $unit.'s';
    }
}
