<?php

namespace Tests\Browser;

use Carbon\Carbon;
use App\Entity\Currency;
use App\Entity\Wallet;
use App\Entity\Money;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class AddLotTest extends DuskTestCase
{
    public function testAddLot()
    {
        $this->browse(function (Browser $browser) {

            $currency = factory(Currency::class)->create();
            $userWallet = factory(Wallet::class)->create();
            $userMoney = factory(Money::class)->create([
                'currency_id' => $currency->id,
                'wallet_id' => $userWallet->id
            ]);

            $browser->loginAs($userWallet->user_id)
                ->visit('/market/lots/add')
                ->assertPathIs('/market/lots/add')
                ->type('date_time_open', Carbon::now()->toDateTimeString())
                ->type('date_time_close', Carbon::tomorrow()->toDateTimeString())
                ->type('price', 10)
                ->press('Add')
                ->assertSee('Lot has been added successfully!');
        });
    }

    public function testAddIncorrectLot()
    {
        $this->browse(function (Browser $browser) {

            $currency = factory(Currency::class)->create();
            $userWallet = factory(Wallet::class)->create();
            $userMoney = factory(Money::class)->create([
                'currency_id' => $currency->id,
                'wallet_id' => $userWallet->id
            ]);

            $browser->loginAs($userWallet->user_id)
                ->visit('/market/lots/add')
                ->press('Add')
                ->assertSee('Sorry, error has been occurred:');
        });
    }
}
