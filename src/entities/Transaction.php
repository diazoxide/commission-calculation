<?php

declare(strict_types=1);

namespace Paysera\CommissionTask\entities;

use InvalidArgumentException;

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
     * @return $this
     */
    public function setDate(string $date): Transaction
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return $this
     */
    public function setClient(int $client): Transaction
    {
        if (!isset(self::ALL_CLIENTS[$client])) {
            throw new InvalidArgumentException(sprintf('Invalid transaction type {%d}.', $client));
        }

        $this->client = $client;

        return $this;
    }

    /**
     * @return $this
     */
    public function setType(int $type): Transaction
    {
        if (!isset(self::ALL_TYPES[$type])) {
            throw new InvalidArgumentException(sprintf('Invalid transaction type {%d}.', $type));
        }

        $this->type = $type;

        return $this;
    }

    /**
     * @return $this
     */
    public function setAmount(float $amount): Transaction
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return $this
     */
    public function setCurrency(string $currency): Transaction
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * @return $this
     */
    public function setUserId(int $user_id): Transaction
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getDate(): string
    {
        return $this->date;
    }

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
