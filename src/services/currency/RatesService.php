<?php

declare(strict_types=1);

namespace Paysera\CommissionTask\services\currency;

use Paysera\CommissionTask\services\currency\interfaces\ProviderInterface;
use Paysera\CommissionTask\services\currency\interfaces\RatesServiceInterface;

class RatesService implements RatesServiceInterface
{
    private ProviderInterface $provider;

    /**
     * RatesService constructor.
     */
    public function __construct(ProviderInterface $provider)
    {
        $this->provider = $provider;
    }

    /**
     * @return $this
     */
    public function setProvider(ProviderInterface $provider): static
    {
        $this->provider = $provider;

        return $this;
    }

    public function convert(float $amount, string $currency, string $from): float
    {
        $rate = $this->provider->getRate($currency, $from);

        return $amount * $rate;
    }
}
