<?php

namespace diazoxide\TransactionsFeeCalculator\Tests\services;

use Exception;
use diazoxide\TransactionsFeeCalculator\entities\Transaction;
use diazoxide\TransactionsFeeCalculator\helpers\AmountFormatter;
use diazoxide\TransactionsFeeCalculator\repositories\TransactionsRepository;
use diazoxide\TransactionsFeeCalculator\services\currency\interfaces\RatesServiceInterface;
use diazoxide\TransactionsFeeCalculator\services\currency\RatesService;
use diazoxide\TransactionsFeeCalculator\services\FeeCalculatorService;
use diazoxide\TransactionsFeeCalculator\Tests\RatesProviderMock;
use PHPUnit\Framework\TestCase;

class FeeCalculatorServiceTest extends TestCase
{

    private TransactionsRepository $transactions;
    private FeeCalculatorService $calculator;

    protected function setUp(): void
    {
        $this->transactions     = new TransactionsRepository();

        $rates_service          = new RatesService(new RatesProviderMock());
        $this->calculator       = new FeeCalculatorService($this->transactions, $rates_service);

        $transactions_to_import = json_decode(file_get_contents(__DIR__ . '/../fixtures/transactions.json'));

        foreach ($transactions_to_import as $transaction_data) {
            $this->transactions->addTransaction(
                (new Transaction())
                    ->setAmount($transaction_data->amount)
                    ->setUserId($transaction_data->user_id)
                    ->setCurrency($transaction_data->currency)
                    ->setType($transaction_data->type)
                    ->setClient($transaction_data->client)
                    ->setDate($transaction_data->date)
            );
        }

        parent::setUp(); // TODO: Change the autogenerated stub
    }

    /**
     * @throws Exception
     */
    public function testCalculationShouldBeSucceed(): void
    {
        $expected = json_decode(file_get_contents(__DIR__ . '/../fixtures/transactions_fees.json'), true);

        $result = [];
        foreach ($this->transactions->getTransactions() as $key => $transaction) {
            $fee       = $this->calculator->calculateFee($transaction);
            $formatted = AmountFormatter::format($fee);
            $this->assertEquals((float)$expected[$key], $formatted);
        }
    }

}