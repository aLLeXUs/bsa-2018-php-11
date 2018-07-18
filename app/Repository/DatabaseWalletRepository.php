<?php

namespace App\Repository;

use App\Entity\Wallet;

class DatabaseWalletRepository implements Contracts\WalletRepository
{
    public function add(Wallet $wallet): Wallet
    {
        // TODO: Implement add() method.
    }

    public function findByUser(int $userId): ?Wallet
    {
        // TODO: Implement findByUser() method.
    }
}