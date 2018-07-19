<?php

namespace App\Response;

use Carbon\Carbon;

class LotResponse implements Contracts\LotResponse
{
    private $id;
    private $userName;
    private $currencyName;
    private $amount;
    private $dateTimeOpen;
    private $dateTimeClose;
    private $price;

    public function __construct(int $id, string $userName, string $currencyName, float $amount,
                                int $dateTimeOpen, int $dateTimeClose, float $price)
    {
        $this->id = $id;
        $this->userName = $userName;
        $this->currencyName = $currencyName;
        $this->amount = $amount;
        $this->dateTimeOpen = $dateTimeOpen;
        $this->dateTimeClose = $dateTimeClose;
        $this->price = $price;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserName(): string
    {
        return $this->userName;
    }

    public function getCurrencyName(): string
    {
        return $this->currencyName;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getDateTimeOpen(): string
    {
        return Carbon::createFromTimestamp($this->dateTimeOpen)->format('Y/m/d H:i:s');
    }

    public function getDateTimeClose(): string
    {
        return Carbon::createFromTimestamp($this->dateTimeClose)->format('Y/m/d H:i:s');
    }

    public function getPrice(): string
    {
        return $this->price;
    }
}