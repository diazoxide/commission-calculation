<?php

declare(strict_types=1);

namespace Paysera\CommissionTask\entities;

use InvalidArgumentException;
use JetBrains\PhpStorm\ArrayShape;

class Transaction
{
    public const TYPE_DEPOSIT = 1;
    public const TYPE_WITHDRAW = 2;

    public const CLIENT_BUSINESS = 1;
    public const CLIENT_PRIVATE = 2;

    public const ALL_TYPES = [
        self::TYPE_WITHDRAW => 'withdraw',
        self::TYPE_DEPOSIT => 'deposit',
    ];

    public const ALL_CLIENTS = [
        self::CLIENT_BUSINESS => 'business',
        self::CLIENT_PRIVATE => 'private',
    ];
    private string $date;
    private int $user_id;
    private int $type;
    private int $client;
    private float $amount;
    private string $currency;

    /**
     * @return array
     */
    #[ArrayShape(['date' => 'string',
                  'user_id' => 'int',
                  'type' => 'int',
                  'client' => 'int',
                  'amount' => 'float',
                  'currency' => 'string',
    ])]
 public function toArray(): array
 {
     return [
            'date' => $this->date,
            'user_id' => $this->user_id,
            'type' => $this->type,
            'client' => $this->client,
            'amount' => $this->amount,
            'currency' => $this->currency,
        ];
 }

    public function setDate(string $date): Transaction
    {
        $this->date = $date;

        return $this;
    }

    public function setClient(int $client): Transaction
    {
        if (!in_array($client, [self::CLIENT_BUSINESS, self::CLIENT_PRIVATE], true)) {
            throw new InvalidArgumentException(sprintf('Invalid transaction type {%d}.', $client));
        }

        $this->client = $client;

        return $this;
    }

    public function setType(int $type): Transaction
    {
        if (!in_array($type, [self::TYPE_DEPOSIT, self::TYPE_WITHDRAW], true)) {
            throw new InvalidArgumentException(sprintf('Invalid transaction type {%d}.', $type));
        }

        $this->type = $type;

        return $this;
    }

    public function setAmount(float $amount): Transaction
    {
        $this->amount = $amount;

        return $this;
    }

    public function setCurrency(string $currency): Transaction
    {
        $this->currency = $currency;

        return $this;
    }

    public function setUserId(int $user_id): Transaction
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getClient(): int
    {
        return $this->client;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }
}
