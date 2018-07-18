<?php

namespace App\Repository;

use App\Entity\Money;

class DatabaseMoneyRepository implements Contracts\MoneyRepository
{
    public function save(Money $money): Money
    {
        // TODO: Implement save() method.
    }

    public function findByWalletAndCurrency(int $walletId, int $currencyId): ?Money
    {
        // TODO: Implement findByWalletAndCurrency() method.
    }
}