<?php

namespace diazoxide\TransactionsFeeCalculator\Tests\repositories;

use diazoxide\TransactionsFeeCalculator\entities\Transaction;
use diazoxide\TransactionsFeeCalculator\repositories\TransactionsRepository;
use PHPUnit\Framework\TestCase;

class TransactionsRepositoryTest extends TestCase
{
    public function testAddTransactionShouldBeSucceed(): void
    {
        $repository = new TransactionsRepository();
        $this->assertCount(0, $repository->getTransactions());

        $repository->addTransaction(new Transaction());

        $this->assertCount(1, $repository->getTransactions());
    }

    public function testGetTransactionsByUserId(): void
    {
        $repository = new TransactionsRepository();
        $repository->addTransaction(
            (new Transaction())
                ->setUserId(101)
                ->setAmount(500)
        );

        $user_transactions = $repository->getUserTransactions(101);
        $this->assertCount(1, $user_transactions);

        $this->assertEquals(
            500,
            $user_transactions[0]->getAmount()
        );
    }
}