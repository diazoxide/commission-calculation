<?php


namespace diazoxide\TransactionsFeeCalculator\Tests;


use diazoxide\TransactionsFeeCalculator\services\currency\interfaces\ProviderInterface;

class RatesProviderMock implements ProviderInterface
{

    private const RATES_EUR = [
        'USD' => 1.1497,
        'JPY' => 129.54
    ];

    /**
     * @param  string  $currency
     * @param  string  $origin
     *
     * @return float
     */
    public function getRate(string $currency, string $origin): float
    {
        if ($currency === 'EUR' && $origin !== $currency) {
            $rate = self::RATES_EUR[$origin];

            return 1 / $rate;
        }

        return self::RATES_EUR[$currency] ?? 1;
    }
}