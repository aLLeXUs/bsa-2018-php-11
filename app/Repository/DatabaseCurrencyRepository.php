<?php

namespace App\Repository;

use App\Entity\Currency;

class DatabaseCurrencyRepository implements Contracts\CurrencyRepository
{
    public function add(Currency $currency): Currency
    {
        // TODO: Implement add() method.
    }

    public function getById(int $id): ?Currency
    {
        // TODO: Implement getById() method.
    }

    public function getCurrencyByName(string $name): ?Currency
    {
        // TODO: Implement getCurrencyByName() method.
    }

    public function findAll()
    {
        // TODO: Implement findAll() method.
    }
}