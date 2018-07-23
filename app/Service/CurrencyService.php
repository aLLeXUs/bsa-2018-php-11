<?php

namespace App\Service;

use App\Entity\Currency;
use App\Repository\Contracts\CurrencyRepository;
use App\Request\Contracts\AddCurrencyRequest;

class CurrencyService implements Contracts\CurrencyService
{
    private $currencyRepository;

    public function __construct(CurrencyRepository $currencyRepository)
    {
        $this->currencyRepository = $currencyRepository;
    }

    public function addCurrency(AddCurrencyRequest $currencyRequest): Currency
    {
        $currency = new Currency([
            'name' => $currencyRequest->getName(),
        ]);
        return $this->currencyRepository->add($currency);
    }
}