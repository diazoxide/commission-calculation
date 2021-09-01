# How to use

## Simple usage example

Calculate CSV provided transactions fees.

```shell
php cli.php ./tests/fixtures/transactions.csv
```

# Classes

### App:class

Responsible for CLI application

Main *Singleton* instance and container for whole application.

**Example**

```php
use Paysera\CommissionTask\App;

App::getInstance()->run();
```

### TransactionsRepository:class

Can be replaced with production repository that implementing `TransactionRepositoryInterface::class`

**Example**

```php
use Paysera\CommissionTask\entities\Transaction;
use Paysera\CommissionTask\repositories\TransactionsRepository;

$repository = new TransactionsRepository();
$repository->addTransaction(new Transaction());

var_dump($repository->getTransactions());
```

### FeeCalculator::class

Constructor required `$transactionsRepository` to use user previous transactions on calculation.

**Example**

```php
use Paysera\CommissionTask\repositories\TransactionsRepository;

/**
 * @var TransactionsRepository $transactionsRepository
 * */
$calculator = new \Paysera\CommissionTask\services\FeeCalculatorService($transactionsRepository);

foreach($transactionsRepository->getTransactions() as $transaction){
    $fee = $calculator->calculateFee($transaction);
    
    echo $fee.PHP_EOL;
}

```

### AmountFormatter:class

Helper class with static method format. Intended for formatting `amount` float values.

**Example**

```php

echo \Paysera\CommissionTask\helpers\AmountFormatter::format(1891.151);

```

# Tests

Command to run PHPUnit tests

```bash
composer run-script phpunit
```