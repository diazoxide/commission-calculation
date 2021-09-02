<?php

declare(strict_types=1);

namespace diazoxide\TransactionsFeeCalculator\repositories\interfaces;

use diazoxide\TransactionsFeeCalculator\entities\Transaction;

interface TransactionsRepositoryInterface
{
    public function getTransactions(): array;

    /**
     * @return mixed
     */
    public function addTransaction(Transaction $transaction): static;

    public function getUserTransactions(int $user_id): array;
}
