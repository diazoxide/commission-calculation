<?php


namespace Paysera\CommissionTask\services\currency\interfaces;


interface RatesServiceInterface
{
    /**
     * @param  float  $amount
     * @param  string  $currency
     * @param  string  $from
     *
     * @return float
     */
    public function convert(float $amount, string $currency, string $from): float;

    public function setProvider(ProviderInterface $provider): static;
}