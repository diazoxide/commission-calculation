<?php

declare(strict_types=1);

namespace diazoxide\TransactionsFeeCalculator;

use Exception;
use RuntimeException;
use diazoxide\TransactionsFeeCalculator\entities\Transaction;
use diazoxide\TransactionsFeeCalculator\helpers\AmountFormatter;
use diazoxide\TransactionsFeeCalculator\services\FeeCalculatorService;
use diazoxide\TransactionsFeeCalculator\services\currency\RatesService;
use diazoxide\TransactionsFeeCalculator\services\currency\providers\ExchangeRatesApi;
use diazoxide\TransactionsFeeCalculator\services\currency\interfaces\RatesServiceInterface;
use diazoxide\TransactionsFeeCalculator\repositories\TransactionsRepository;
use diazoxide\TransactionsFeeCalculator\repositories\interfaces\TransactionsRepositoryInterface;

final class App
{
    private static self $instance;
    private TransactionsRepositoryInterface $transactions;
    private FeeCalculatorService $fee_calculator;
    private RatesServiceInterface $rates_service;

    /**
     * Singleton for whole app.
     *
     * @return $this
     */
    public static function getInstance(): self
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * App constructor.
     */
    private function __construct()
    {
        $rates_service_provider = new ExchangeRatesApi('6b88fdec20e4da789caa81208eee666a', true);
        $this->rates_service = new RatesService($rates_service_provider);
        $this->transactions = new TransactionsRepository();
        $this->fee_calculator = new FeeCalculatorService($this->transactions, $this->rates_service);
    }

    /**
     * @throws Exception
     */
    public function run(): bool
    {
        global $argv;

        if (!isset($argv[1])) {
            throw new RuntimeException('Please input CSV file path.');
        }

        $csv_file_path = $argv[1];

        if (!file_exists($csv_file_path)) {
            throw new RuntimeException('CSV File not found.');
        }

        $this->fillRepositoryFromCsv($csv_file_path);

        $transactions = $this->transactions->getTransactions();
        foreach ($transactions as $key => $transaction) {
            echo AmountFormatter::format(
                $this->fee_calculator->calculateFee($transaction)
            );

            if (array_key_last($transactions) !== $key) {
                echo PHP_EOL;
            }
        }

        return true;
    }

    private function fillRepositoryFromCsv(string $csv_file_path): void
    {
        $fh = fopen($csv_file_path, 'r');
        while (($row = fgetcsv($fh)) !== false) {
            $transaction = (new Transaction())
                ->setDate($row[0])
                ->setUserId((int) $row[1])
                ->setClient(
                    ([
                        'private' => Transaction::CLIENT_PRIVATE,
                        'business' => Transaction::CLIENT_BUSINESS,
                    ])[$row[2]]
                )
                ->setType(
                    ([
                        'deposit' => Transaction::TYPE_DEPOSIT,
                        'withdraw' => Transaction::TYPE_WITHDRAW,
                    ])[$row[3]]
                )
                ->setAmount((float) $row[4])
                ->setCurrency($row[5]);

            $this->transactions->addTransaction(
                $transaction
            );
        }
    }

    public function getRatesService(): RatesServiceInterface
    {
        return $this->rates_service;
    }

    public function getFeeCalculator(): FeeCalculatorService
    {
        return $this->fee_calculator;
    }

    public function getTransactions(): TransactionsRepositoryInterface
    {
        return $this->transactions;
    }
}
