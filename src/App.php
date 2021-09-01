<?php

declare(strict_types=1);

namespace Paysera\CommissionTask;

use Exception;
use JetBrains\PhpStorm\Pure;
use Paysera\CommissionTask\entities\Transaction;
use Paysera\CommissionTask\helpers\AmountFormatter;
use Paysera\CommissionTask\repositories\interfaces\TransactionsRepositoryInterface;
use Paysera\CommissionTask\repositories\TransactionsRepository;
use Paysera\CommissionTask\services\currency\interfaces\RatesServiceInterface;
use Paysera\CommissionTask\services\currency\providers\ExchangeRatesApi;
use Paysera\CommissionTask\services\currency\RatesService;
use Paysera\CommissionTask\services\FeeCalculator;
use RuntimeException;

class App
{
    private static self $instance;

    private TransactionsRepositoryInterface $transactions;

    private FeeCalculator $fee_calculator;

    private RatesServiceInterface $rates_service;

    /**
     * Singleton for whole app.
     *
     * @return $this
     */
    public static function getInstance(): self
    {
        if ( ! isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * App constructor.
     */
    #[Pure] private function __construct()
    {
        $rates_service_provider = new ExchangeRatesApi('6b88fdec20e4da789caa81208eee666a', true);
        $this->rates_service    = new RatesService($rates_service_provider);
        $this->transactions     = new TransactionsRepository();
        $this->fee_calculator   = new FeeCalculator($this->transactions, $this->rates_service);
    }

    /**
     * @throws Exception
     */
    public function run(): bool
    {
        global $argv;

        if ( ! isset($argv[1])) {
            throw new RuntimeException('Please input CSV file path.');
        }

        $csv_file_path = $argv[1];

        if ( ! file_exists($csv_file_path)) {
            throw new RuntimeException('CSV File not found.');
        }

        $this->prepareCsv($csv_file_path);

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

    /**
     * @param  string  $file_path
     */
    private function prepareCsv(string $file_path): void
    {
        $fh = fopen($file_path, 'r');
        while (($row = fgetcsv($fh)) !== false) {
            $transaction = (new Transaction())
                ->setDate($row[0])
                ->setUserId((int) $row[1])
                ->setClient(
                    ([
                        'private'  => Transaction::CLIENT_PRIVATE,
                        'business' => Transaction::CLIENT_BUSINESS,
                    ])[$row[2]]
                )
                ->setType(
                    ([
                        'deposit'  => Transaction::TYPE_DEPOSIT,
                        'withdraw' => Transaction::TYPE_WITHDRAW,
                    ])[$row[3]]
                )
                ->setAmount((float)$row[4])
                ->setCurrency($row[5]);

            $this->transactions->addTransaction(
                $transaction
            );
        }
    }

    /**
     * @return RatesServiceInterface
     */
    public function getRatesService(): RatesServiceInterface
    {
        return $this->rates_service;
    }

    /**
     * @return FeeCalculator
     */
    public function getFeeCalculator(): FeeCalculator
    {
        return $this->fee_calculator;
    }

    /**
     * @return TransactionsRepositoryInterface
     */
    public function getTransactions(): TransactionsRepositoryInterface
    {
        return $this->transactions;
    }
}
