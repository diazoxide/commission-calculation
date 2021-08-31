<?php

namespace Paysera\CommissionTask\services;

use JetBrains\PhpStorm\Pure;
use Paysera\CommissionTask\entities\Transaction;

abstract class FeeCalculator
{
    protected const COMMISSION_RATE = 0.03;

    #[Pure] public function calculateFee(Transaction $transaction): float
    {
        return $transaction->getAmount() * static::COMMISSION_RATE;
    }

}