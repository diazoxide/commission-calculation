<?php

declare(strict_types=1);

namespace Paysera\CommissionTask\repositories;

use Paysera\CommissionTask\entities\Transaction;

interface TransactionsRepositoryInterface
{
    public function getTransactions(): array;

    /**
     * @return mixed
     */
    public function addTransaction(Transaction $transaction): static;

    public function getUserTransactions(int $user_id): array;
}
