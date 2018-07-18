<?php

namespace App\Service;

use App\Entity\Currency;
use App\Request\Contracts\AddCurrencyRequest;

class CurrencyService implements Contracts\CurrencyService
{
    public function addCurrency(AddCurrencyRequest $currencyRequest): Currency
    {
        // TODO: Implement addCurrency() method.
    }
}