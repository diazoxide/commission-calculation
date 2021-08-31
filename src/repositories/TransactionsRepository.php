<?php

declare(strict_types=1);

namespace Paysera\CommissionTask\repositories;

use JetBrains\PhpStorm\Pure;
use Paysera\CommissionTask\entities\Transaction;

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
     * @param int $user_id
     *
     * @return Transaction[]
     */
    #[Pure]
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
