<?php

namespace App\Request;

use Illuminate\Foundation\Http\FormRequest;

class AddLotRequest extends FormRequest implements Contracts\AddLotRequest
{
    public function getCurrencyId(): int
    {
        return request()->input('currency_id');
    }

    public function getSellerId(): int
    {
        return request()->input('seller_id');
    }

    public function getDateTimeOpen(): int
    {
        return request()->input('date_time_open');
    }

    public function getDateTimeClose(): int
    {
        return request()->input('date_time_close');
    }

    public function getPrice(): float
    {
        return request()->input('price');
    }
}