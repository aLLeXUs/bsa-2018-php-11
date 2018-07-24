<?php

namespace Tests\Feature;

use App\Entity\Currency;
use App\Entity\Lot;
use App\Entity\Money;
use App\Entity\Trade;
use App\Entity\Wallet;
use App\User;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LotsTest extends TestCase
{
    use RefreshDatabase;

    public function testGetLot()
    {
        $currency = factory(Currency::class)->create();
        $userWallet = factory(Wallet::class)->create();
        $userMoney = factory(Money::class)->create([
            'currency_id' => $currency->id,
            'wallet_id' => $userWallet->id
        ]);
        $lot = factory(Lot::class)->create([
            'currency_id' => $currency->id,
            'seller_id' => $userWallet->user_id
        ]);
        $response = $this->json('GET', "/api/v1/lots/$lot->id");
        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'user_name',
                'currency_name',
                'amount',
                'date_time_open',
                'date_time_close',
                'price'
            ]);
    }

    public function testGetLots()
    {
        for ($i=0; $i<5; $i++) {
            $currency = factory(Currency::class)->create();
            $userWallet = factory(Wallet::class)->create();
            $userMoney = factory(Money::class)->create([
                'currency_id' => $currency->id,
                'wallet_id' => $userWallet->id
            ]);
            $lot = factory(Lot::class)->create([
                'currency_id' => $currency->id,
                'seller_id' => $userWallet->user_id
            ]);
        }
        $response = $this->json('GET', "/api/v1/lots");
        $response->assertStatus(200);
    }

    public function testAddLot()
    {
        $currency = factory(Currency::class)->create();
        $userWallet = factory(Wallet::class)->create();
        $userMoney = factory(Money::class)->create([
            'currency_id' => $currency->id,
            'wallet_id' => $userWallet->id
        ]);

        $response = $this->actingAs(User::find($userWallet->user_id))
            ->json('POST', '/api/v1/lots',[
                'currency_id' => $currency->id,
                'date_time_open' => Carbon::now()->getTimestamp(),
                'date_time_close' => Carbon::tomorrow()->getTimestamp(),
                'price' => 500
            ]);
        $response->assertStatus(201);
    }

    public function testBuyLot()
    {
        $currency = factory(Currency::class)->create();
        $userWallet = factory(Wallet::class)->create();
        $userMoney = factory(Money::class)->create([
            'currency_id' => $currency->id,
            'wallet_id' => $userWallet->id
        ]);
        $lot = factory(Lot::class)->create([
            'currency_id' => $currency->id,
            'seller_id' => $userWallet->user_id
        ]);
        $buyerWallet = factory(Wallet::class)->create();
        $buyerMoney = factory(Money::class)->create([
            'currency_id' => $currency->id,
            'wallet_id' => $buyerWallet->id
        ]);

        $response = $this->actingAs(User::find($buyerWallet->user_id))
            ->json('POST', '/api/v1/trades',[
                'lot_id' => $lot->id,
                'amount' => 500
            ]);
        echo '----------------------';
        echo $response->content();
        echo '----------------------';
        $response->assertStatus(201);
    }
}
