<?php

declare(strict_types=1);

namespace Paysera\CommissionTask\services\currency\interfaces;

interface ProviderInterface
{
    public function getRate(string $currency, string $origin): float;
}
