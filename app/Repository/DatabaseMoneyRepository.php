<?php

namespace App\Repository;

use App\Entity\Money;

class DatabaseMoneyRepository implements Contracts\MoneyRepository
{
    public function save(Money $money): Money
    {
        $money->save();
        return $money;
    }

    public function findByWalletAndCurrency(int $walletId, int $currencyId): ?Money
    {
        return Money::where('wallet_id', $walletId)
            ->where('currency_id', $currencyId)->first();
    }
}