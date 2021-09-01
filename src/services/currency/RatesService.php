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
     *
     * @param  ProviderInterface  $provider
     */
    public function __construct(ProviderInterface $provider)
    {
        $this->provider = $provider;
    }

    /**
     * @param  ProviderInterface  $provider
     *
     * @return $this
     */
    public function setProvider(ProviderInterface $provider): static
    {
        $this->provider = $provider;

        return $this;
    }

    /**
     * @param  float  $amount
     * @param  string  $currency
     * @param  string  $from
     *
     * @return float
     */
    public function convert(float $amount, string $currency, string $from): float
    {
        $rate = $this->provider->getRate($currency, $from);

        return $amount * $rate;
    }
}