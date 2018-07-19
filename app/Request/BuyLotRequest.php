<?php

namespace App\Request;

use Illuminate\Foundation\Http\FormRequest;

class BuyLotRequest extends FormRequest implements Contracts\BuyLotRequest
{
    public function getUserId(): int
    {
        return request()->input('user_id');
    }

    public function getLotId(): int
    {
        return request()->input('id');
    }

    public function getAmount(): float
    {
        return request()->input('amount');
    }
}