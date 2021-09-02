<?php

declare(strict_types=1);

namespace diazoxide\TransactionsFeeCalculator\helpers;

class AmountFormatter
{
    public static function format(float $amount): string
    {
        $amount = self::roundUp($amount, 3);

        if ($amount > 999) {
            $amount = self::roundUp($amount, 0);
        }

        $formatted = number_format($amount, 2, '.', '');

        return substr($formatted, 0, 4);
    }

    private static function roundUp(float $number, int $precision = 2): float|int
    {
        $fig = (int) str_pad('1', $precision, '0');

        return ceil($number * $fig) / $fig;
    }
}
