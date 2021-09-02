<?php

declare(strict_types=1);

namespace diazoxide\TransactionsFeeCalculator\repositories;

use diazoxide\TransactionsFeeCalculator\entities\Transaction;
use diazoxide\TransactionsFeeCalculator\repositories\interfaces\TransactionsRepositoryInterface;

class TransactionsRepository implements TransactionsRepositoryInterface
{
    /**
     * @var Transaction[]
     * */
    private array $transactions = [];

    /**
     * @return Transaction[]
     */
    public function getTransactions(): array
    {
        return $this->transactions;
    }

    /**
     * @return TransactionsRepository
     */
    public function addTransaction(Transaction $transaction): static
    {
        $this->transactions[] = $transaction;

        return $this;
    }

    /**
     * @return Transaction[]
     */
    public function getUserTransactions(int $user_id): array
    {
        $result = [];
        foreach ($this->transactions as $transaction) {
            if ($transaction->getUserId() === $user_id) {
                $result[] = $transaction;
            }
        }

        return $result;
    }
}
