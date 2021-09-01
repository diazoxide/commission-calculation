<?php


namespace Paysera\CommissionTask\services\currency\interfaces;


interface ProviderInterface
{
    public function getRate(string $currency, string $origin): float;

//http://api.exchangeratesapi.io/v1/latest?access_key=6b88fdec20e4da789caa81208eee666a&format=1
}