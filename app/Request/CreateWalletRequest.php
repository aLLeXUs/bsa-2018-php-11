<?php

namespace App\Request;

use Illuminate\Foundation\Http\FormRequest;

class CreateWalletRequest extends FormRequest implements Contracts\CreateWalletRequest
{
    public function getUserId(): int
    {
        return request()->input('user_id');
    }
}