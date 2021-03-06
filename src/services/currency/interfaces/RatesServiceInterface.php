<?php

declare(strict_types=1);

namespace diazoxide\TransactionsFeeCalculator\services\currency\interfaces;

interface RatesServiceInterface
{
    public function convert(float $amount, string $currency, string $from): float;

    /**
     * @return $this
     */
    public function setProvider(ProviderInterface $provider): static;
}
