<?php

declare(strict_types=1);

namespace Paysera\CommissionTask\services\currency\providers;

use RuntimeException;
use InvalidArgumentException;
use Paysera\CommissionTask\services\currency\interfaces\ProviderInterface;

class ExchangeRatesApi implements ProviderInterface
{
    private const FREE_KEY_SUPPORTED_BASES = ['EUR'];

    private bool $is_free_key;
    private string $api_url = 'http://api.exchangeratesapi.io/v1/latest?access_key=%s&base=%s&format=1';
    private string $api_key;

    private array $currencies_data = [];

    /**
     * ExchangeRatesApi constructor.
     */
    public function __construct(string $api_key, bool $is_free_key)
    {
        $this->api_key = $api_key;
        $this->is_free_key = $is_free_key;
    }

    private function getUrl(string $base_currency): string
    {
        return sprintf($this->api_url, $this->api_key, $base_currency);
    }

    public function getRates(string $origin): array
    {
        if ($this->is_free_key && $origin !== 'EUR') {
            throw new InvalidArgumentException('Origin is not supporting for free api key');
        }

        if (!isset($this->currencies_data[$origin])) {
            $json = file_get_contents($this->getUrl($origin));

            if (!$json) {
                throw new RuntimeException('Can\'t connect '.self::class.' remote url.');
            }
            $data = json_decode($json, true);

            if (!isset($data['rates']) || empty($data['rates']) || !is_array($data['rates'])) {
                throw new RuntimeException('Can\'t get currency rates.');
            }

            $this->currencies_data[$origin] = $data['rates'];
        }

        return $this->currencies_data[$origin];
    }

    public function getRate(string $currency, string $origin): float
    {
        if ($this->is_free_key) {
            if (!in_array($origin, self::FREE_KEY_SUPPORTED_BASES, true)
                 && in_array(
                     $currency,
                     self::FREE_KEY_SUPPORTED_BASES,
                     true
                 )) {
                $rates = $this->getRates($currency);

                $rate = (float) (1 / $rates[$origin]);
            }
        }

        if (!isset($rates)) {
            $rates = $this->getRates($origin);

            if (isset($rates[$currency])) {
                $rate = (float) $rates[$currency];
            }
        }

        if (!isset($rate)) {
            throw new RuntimeException('Currency code "'.$currency.'" not defined.');
        }

        return $rate;
    }
}
