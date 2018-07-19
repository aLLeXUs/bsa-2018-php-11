<?php

namespace Tests\Unit;

use App\Entity\Lot;
use App\Request\AddCurrencyRequest;
use App\Request\AddLotRequest;
use App\Request\CreateWalletRequest;
use App\Request\MoneyRequest;
use App\Service\Contracts\CurrencyService;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Service\Contracts\WalletService;
use App\Service\Contracts\MarketService;
use App\User;

class MarketServiceTest extends TestCase
{
    use RefreshDatabase;

    private $currencyService;
    private $walletService;
    private $marketService;

    protected function setUp()
    {
        parent::setUp();
        $this->currencyService = $this->app->make(CurrencyService::class);
        $this->walletService = $this->app->make(WalletService::class);
        $this->marketService = $this->app->make(MarketService::class);
    }

    public function testGetLotListIsEmpty()
    {
        $lotList = $this->marketService->getLotList();
        $this->assertEmpty($lotList);
    }

    public function testInstance()
    {
        $this->assertInstanceOf(
            WalletService::class,
            $this->walletService
        );
        $this->assertInstanceOf(
            marketService::class,
            $this->marketService
        );
    }

    public function testAddLot()
    {
        $user = new User([
            'name' => 'User1',
            'email' => 'user@example.com',
            'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret,
        ]);
        $user->save();
        $this->assertEquals($user->id, '1');

        $currency = $this->currencyService->addCurrency(new AddCurrencyRequest('Bitcoin'));
        $this->assertEquals($currency->name, 'Bitcoin');

        $wallet = $this->walletService->addWallet(new CreateWalletRequest($user->id));
        $money = $this->walletService->addMoney(new MoneyRequest($wallet->id, $currency->id, 100));

        $lot = $this->marketService->addLot(new AddLotRequest($currency->id, $user->id, Carbon::now()->getTimestamp(), Carbon::tomorrow()->getTimestamp(), 5));

        $this->assertInstanceOf(Lot::class, $lot);
    }
}
