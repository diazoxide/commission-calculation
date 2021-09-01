<?php

declare(strict_types=1);

namespace Paysera\CommissionTask\services;

use Exception;
use Paysera\CommissionTask\entities\Transaction;
use Paysera\CommissionTask\repositories\TransactionsRepository;
use Paysera\CommissionTask\services\currency\interfaces\RatesServiceInterface;

class FeeCalculatorService
{
    private TransactionsRepository $transactions;

    private RatesServiceInterface $rates_service;

    /**
     * FeeCalculator constructor.
     */
    public function __construct(TransactionsRepository $transactions, RatesServiceInterface $rates_service)
    {
        $this->transactions = $transactions;
        $this->rates_service = $rates_service;
    }

    public function toEur(float $amount, string $currency): float
    {
        return $this->rates_service->convert($amount, 'EUR', $currency);
    }

    private function fromEurTo(float $amount, string $currency): float
    {
        return $this->rates_service->convert($amount, $currency, 'EUR');
    }

    /**
     * Calculate Fee for transaction.
     *
     * @throws Exception
     */
    public function calculateFee(Transaction $transaction): float
    {
        $fee = 0.0;
        if ($transaction->getType() === Transaction::TYPE_DEPOSIT) {
            $fee = $transaction->getAmount() * 0.03 / 100;
        } elseif ($transaction->getType() === Transaction::TYPE_WITHDRAW) {
            if ($transaction->getClient() === Transaction::CLIENT_PRIVATE) {
                // Getting user old transactions to calculate weekly limit
                $user_transactions = $this->transactions->getUserTransactions($transaction->getUserId());
                $sum = 0;
                $total_withdraw_count = 0;
                foreach ($user_transactions as $user_transaction) {
                    // Breaking loop to avoid including newest transactions amount on limit
                    if ($transaction === $user_transaction) {
                        break;
                    }

                    // Check only withdraw transactions
                    if ($user_transaction->getType() === Transaction::TYPE_WITHDRAW) {
                        $user_transaction_timestamp = strtotime($user_transaction->getDate());
                        $transaction_timestamp = strtotime($transaction->getDate());
                        if (
                            $user_transaction_timestamp >= strtotime('monday this week', $transaction_timestamp) &&
                            $user_transaction_timestamp <= strtotime('sunday this week', $transaction_timestamp)
                        ) {
                            ++$total_withdraw_count;
                            $sum += $this->rates_service->convert(
                                $user_transaction->getAmount(),
                                'EUR',
                                $user_transaction->getCurrency()
                            );
                        }
                    }
                }

                /*
                 * Checking if total withdraw limit is not over and total withdraw sum is not over too
                 * Then calculate fee only for exceeded amount
                 * */
                if ($total_withdraw_count < 3 && $sum < 1000) {
                    $amount_for_commissioning_on_eur = $sum + $this->rates_service->convert(
                        $transaction->getAmount(),
                        'EUR',
                        $transaction->getCurrency()
                    ) - 1000;

                    if ($amount_for_commissioning_on_eur > 0) {
                        $fee = $this->rates_service->convert(
                            $amount_for_commissioning_on_eur,
                            $transaction->getCurrency(),
                            'EUR'
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
