<?php

declare(strict_types=1);

namespace Paysera\CommissionTask\services\currency\interfaces;

interface RatesServiceInterface
{
    public function convert(float $amount, string $currency, string $from): float;

    /**
     * @return $this
     */
    public function setProvider(ProviderInterface $provider): static;
}
