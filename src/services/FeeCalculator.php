<?php

declare(strict_types=1);

namespace Paysera\CommissionTask\services;

use Exception;
use JetBrains\PhpStorm\Pure;
use Paysera\CommissionTask\entities\Transaction;
use Paysera\CommissionTask\repositories\TransactionsRepository;

class FeeCalculator
{
    private TransactionsRepository $transactions;

    /**
     * FeeCalculator constructor.
     */
    public function __construct(TransactionsRepository $transactions)
    {
        $this->transactions = $transactions;
    }

    public function toEur(float $amount, string $currency): float
    {
        if ($currency === 'USD') {
            return $amount / 1.1497;
        }
        if ($currency === 'JPY') {
            return $amount / 129.53;
        }

        return $amount;
    }

    private function fromEurTo(float $amount, string $currency): float
    {
        if ($currency === 'USD') {
            return $amount * 1.1497;
        }
        if ($currency === 'JPY') {
            return $amount * 129.53;
        }

        return $amount;
    }

    /**
     * @throws Exception
     */
    #[Pure]
 public function calculateFee(Transaction $transaction): float
 {
     $fee = 0.0;
     if ($transaction->getType() === Transaction::TYPE_DEPOSIT) {
         $fee = $transaction->getAmount() * 0.03 / 100;
     } elseif ($transaction->getType() === Transaction::TYPE_WITHDRAW) {
         if ($transaction->getClient() === Transaction::CLIENT_PRIVATE) {
             $user_transactions = $this->transactions->getUserTransactions($transaction->getUserId());
             $sum = 0;
             $total_withdraw_count = 0;
             foreach ($user_transactions as $user_transaction) {
                 if ($transaction === $user_transaction) {
                     break;
                 }

                 if ($user_transaction->getType() === Transaction::TYPE_WITHDRAW) {
                     $user_transaction_timestamp = strtotime($user_transaction->getDate());
                     $transaction_timestamp = strtotime($transaction->getDate());
                     if (
                            $user_transaction_timestamp >= strtotime('monday this week', $transaction_timestamp) &&
                            $user_transaction_timestamp <= strtotime('sunday this week', $transaction_timestamp)
                        ) {
                         ++$total_withdraw_count;
                         $sum += $this->toEur(
                                $user_transaction->getAmount(),
                                $user_transaction->getCurrency()
                            );
                     }
                 }
             }

             if ($total_withdraw_count < 3 && $sum < 1000) {
                 $amount_for_commissioning_on_eur = $sum + $this->toEur(
                            $transaction->getAmount(),
                            $transaction->getCurrency()
                        ) - 1000;

                 if ($amount_for_commissioning_on_eur > 0) {
                     $fee = $this->fromEurTo(
                                $amount_for_commissioning_on_eur,
                                $transaction->getCurrency()
                            ) * 0.3 / 100;
                 }
             } else {
                 $fee = $transaction->getAmount() * 0.3 / 100;
             }
         } elseif ($transaction->getClient() === Transaction::CLIENT_BUSINESS) {
             $fee = $transaction->getAmount() * 0.5 / 100;
         }
     }

     return $fee;
 }
}
