<?php

namespace App\Request;

use Illuminate\Foundation\Http\FormRequest;

class AddCurrencyRequest extends FormRequest implements Contracts\AddCurrencyRequest
{
    public function getName(): string
    {
        return request()->input('name');
    }
}