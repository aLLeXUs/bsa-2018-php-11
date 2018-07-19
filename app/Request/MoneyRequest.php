<?php

namespace App\Request;

class MoneyRequest implements Contracts\MoneyRequest
{
    private $walletId;
    private $currencyId;
    private $amount;

    public function __construct(int $walletId, int $currencyId, float $amount)
    {
        $this->walletId = $walletId;
        $this->currencyId = $currencyId;
        $this->amount = $amount;
    }

    public function getWalletId(): int
    {
        return request()->input('wallet_id');
    }

    public function getCurrencyId(): int
    {
        return request()->input('currency_id');
    }

    public function getAmount(): float
    {
        return request()->input('amount');
    }
}