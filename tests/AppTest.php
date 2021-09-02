<?php

declare(strict_types=1);

namespace diazoxide\TransactionsFeeCalculator\Tests;

use Exception;
use diazoxide\TransactionsFeeCalculator\App;
use PHPUnit\Framework\TestCase;

class AppTest extends TestCase
{
    private App $app;

    public function setUp(): void
    {
        $this->app = App::getInstance();

        $this->app->getRatesService()->setProvider(
            new RatesProviderMock()
        );
    }

    /**
     * @throws Exception
     */
    public function testRunShouldFailWhenCsvFileNotFound(): void
    {
        global $argv;
        $argv[1] = __DIR__ . '/fixtures/wrong_transactions.csv';
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('CSV File not found.');
        $this->app->run();
    }

    /**
     * @throws Exception
     */
    public function testRunShouldFailWhenFileArgumentNotProvided(): void
    {
        global $argv;
        unset($argv[1]);
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Please input CSV file path.');
        $this->app->run();
    }

    /**
     * @throws Exception
     */
    public function testSuccessfulRun():void
    {
        global $argv;
        unset($argv[1]);
        $argv[1] = __DIR__ . '/fixtures/transactions.csv';
        $this->setOutputCallback(
            function ($output) {
                $this->assertEquals($output, file_get_contents(__DIR__ . '/fixtures/app_output.txt'));
            }
        );
        $success = $this->app->run();
        $this->assertTrue($success);
    }
}
