<?php

declare(strict_types=1);

namespace Paysera\CommissionTask\Tests;

use Paysera\CommissionTask\App;
use PHPUnit\Framework\TestCase;

class AppTest extends TestCase
{
    private App $app;

    public function setUp(): void
    {
        $this->app = App::getInstance();
    }

    public function testRunShouldFailWhenCsvFileNotFound()
    {
        global $argv;
        $argv[1] = __DIR__ . '/fixtures/wrong_transactions.csv';
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('CSV File not found.');
        $this->app->run();
    }

    public function testRunShouldFailWhenFileArgumentNotProvided()
    {
        global $argv;
        unset($argv[1]);
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Please input CSV file path.');
        $this->app->run();
    }

    public function testSuccessfulRun()
    {
        global $argv;
        unset($argv[1]);
        $argv[1] = __DIR__ . '/fixtures/transactions_1.csv';
        $this->setOutputCallback(
            function ($output) {
                $this->assertEquals($output,file_get_contents(__DIR__ . '/fixtures/app_output.txt'));
            }
        );
        $success = $this->app->run();
        $this->assertTrue($success);
    }
}
