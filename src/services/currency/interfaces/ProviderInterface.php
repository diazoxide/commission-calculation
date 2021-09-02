<?php

declare(strict_types=1);

namespace diazoxide\TransactionsFeeCalculator\services\currency\interfaces;

interface ProviderInterface
{
    public function getRate(string $currency, string $origin): float;
}
