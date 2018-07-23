<?php

namespace Tests\Unit;

use App\Entity\Currency;
use App\Entity\Lot;
use App\Entity\Money;
use App\Entity\Trade;
use App\Entity\Wallet;
use App\Mail\TradeCreated;
use App\Repository\DatabaseCurrencyRepository;
use App\Repository\DatabaseLotRepository;
use App\Repository\DatabaseMoneyRepository;
use App\Repository\DatabaseTradeRepository;
use App\Repository\DatabaseUserRepository;
use App\Repository\DatabaseWalletRepository;
use App\Request\AddCurrencyRequest;
use App\Request\AddLotRequest;
use App\Request\BuyLotRequest;
use App\Request\CreateWalletRequest;
use App\Request\MoneyRequest;
use App\Response\LotResponse;
use App\Service\Contracts\CurrencyService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use App\Service\Contracts\WalletService;
use App\Service\Contracts\MarketService;
use App\User;

class MarketServiceTest extends TestCase
{
    private $lotRepository;
    private $currencyRepository;
    private $userRepository;
    private $walletRepository;
    private $moneyRepository;
    private $tradeRepository;

    private $currencyService;
    private $walletService;
    private $marketService;

    protected function setUp()
    {
        parent::setUp();

        $this->lotRepository = $this->createMock(DatabaseLotRepository::class);
        $this->currencyRepository = $this->createMock(DatabaseCurrencyRepository::class);
        $this->userRepository = $this->createMock(DatabaseUserRepository::class);
        $this->walletRepository = $this->createMock(DatabaseWalletRepository::class);
        $this->moneyRepository = $this->createMock(DatabaseMoneyRepository::class);
        $this->tradeRepository = $this->createMock(DatabaseTradeRepository::class);

        $this->currencyRepository->method('add')->will($this->returnCallback(function ($arg) {
            return self::returnModelWithId($arg);
        }));
//        $this->lotRepository->method('getById')->willReturn(new Currency([
//                'id' => $this->returnArgument(0),
//                'name' => 'Bitcoin'
//            ]));
        $this->lotRepository->method('add')->will($this->returnCallback(function ($arg) {
            return self::returnModelWithId($arg);
        }));
//        $this->lotRepository->method('getById')->will($this->callback(function ($id) {
//            return new Lot([
//                'id' => $id,
//                'currency_id' => $currency->id,
//                'seller_id' => $seller->id,
//                'date_time_open' => Carbon::now(),
//                'date_time_close' => Carbon::tomorrow(),
//                'price' => 4
//            ]);
//        }));
        $this->walletRepository->method('add')->will($this->returnCallback(function ($arg) {
            return self::returnModelWithId($arg);
        }));
        $this->moneyRepository->method('save')->will($this->returnCallback(function ($arg) {
            return self::returnModelWithId($arg);
        }));
        $this->tradeRepository->method('add')->will($this->returnCallback(function ($arg) {
            return self::returnModelWithId($arg);
        }));

//        $this->currencyService = $this->createMock(CurrencyService::class);
//        $this->walletService = $this->createMock(WalletService::class);

        $this->currencyService = new \App\Service\CurrencyService($this->currencyRepository);
        $this->walletService = new \App\Service\WalletService($this->moneyRepository, $this->walletRepository);
        $this->marketService = new \App\Service\MarketService($this->lotRepository, $this->currencyRepository,
            $this->userRepository, $this->walletRepository, $this->moneyRepository,
            $this->tradeRepository,$this->walletService);
    }

    public function testInstance()
    {
        $this->assertInstanceOf(
            MarketService::class,
            $this->marketService
        );
    }

    public function testAddLot()
    {
        $user = new User([
            'id' => 1,
            'name' => 'User1',
            'email' => 'user@example.com',
            'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret,
        ]);
        $this->assertEquals($user->id, '1');

        $addCurrencyRequest = new AddCurrencyRequest('Bitcoin');
        $currency = $this->currencyService->addCurrency($addCurrencyRequest);
        $this->assertInstanceOf(Currency::class, $currency);
        $this->assertEquals($currency->name, $addCurrencyRequest->getName());

        $createWalletRequest = new CreateWalletRequest($user->id);
        $wallet = $this->walletService->addWallet($createWalletRequest);
        $this->assertInstanceOf(Wallet::class, $wallet);
        $this->assertEquals($createWalletRequest->getUserId(), $wallet->user_id);

        $moneyRequest = new MoneyRequest($wallet->id, $currency->id, 100);
        $money = $this->walletService->addMoney($moneyRequest);
        $this->assertInstanceOf(Money::class, $money);
        $this->assertEquals($moneyRequest->getWalletId(), $money->wallet_id);
        $this->assertEquals($moneyRequest->getCurrencyId(), $money->currency_id);
        $this->assertEquals($moneyRequest->getAmount(), $money->amount);

        $addLotRequest = new AddLotRequest($currency->id, $user->id, Carbon::now()->getTimestamp(), Carbon::tomorrow()->getTimestamp(), 5);
        $this->lotRepository->method('findAllActiveLots')->willReturn(new Collection());
        $lot = $this->marketService->addLot($addLotRequest);
        $this->assertInstanceOf(Lot::class, $lot);
        $this->assertEquals($addLotRequest->getCurrencyId(), $lot->currency_id);
        $this->assertEquals($addLotRequest->getSellerId(), $lot->seller_id);
        $this->assertEquals($addLotRequest->getDateTimeOpen(), $lot->date_time_open->getTimestamp());
        $this->assertEquals($addLotRequest->getDateTimeClose(), $lot->date_time_close->getTimestamp());
        $this->assertEquals($addLotRequest->getPrice(), $lot->price);
    }

    public function testBuyLot()
    {
        Mail::fake();

        $user = new User([
            'id' => 1,
            'name' => 'User1',
            'email' => 'user@example.com',
            'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret,
        ]);

        $seller = new User([
            'id' => 2,
            'name' => 'User2',
            'email' => 'user2@example.com',
            'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret,
        ]);

        $currency = new Currency([
            'id' => 1,
            'name' => 'Bitcoin'
        ]);

        $lot = new Lot([
            'id' => 1,
            'currency_id' => $currency->id,
            'seller_id' => $seller->id,
            'date_time_open' => Carbon::now(),
            'date_time_close' => Carbon::tomorrow(),
            'price' => 4
        ]);

        $buyerWallet = new Wallet([
            'id' => 1,
            'user_id' => $user->id
        ]);
        $buyerMoney = new Money([
            'id' => 1,
            'wallet_id' => $buyerWallet->id,
            'currency_id' => $currency->id,
            'amount' => 500
        ]);

        $sellerWallet = new Wallet([
            'id' => 2,
            'user_id' => $seller->id
        ]);
        $sellerMoney = new Money([
            'id' => 2,
            'wallet_id' => $sellerWallet->id,
            'currency_id' => $currency->id,
            'amount' => 500
        ]);

        $buyLotRequest = new BuyLotRequest($user->id, $lot->id, '2');
        $this->walletRepository->method('findByUser')->will($this->returnValueMap([
            [$seller->id, $sellerWallet],
            [$user->id, $buyerWallet]
        ]));
        $this->lotRepository->method('isActiveById')->willReturn(true);
        $this->lotRepository->method('findActiveLot')->willReturn($lot);
        $this->userRepository->method('getById')->willReturn($seller);
        $this->moneyRepository->method('findByWalletAndCurrency')->willReturn($buyerMoney);
        $trade = $this->marketService->buyLot($buyLotRequest);
        $this->assertInstanceOf(Trade::class, $trade);
        Mail::assertSent(TradeCreated::class);
    }

    public function testGetLot()
    {
        $currency = $this->currencyService->addCurrency(new AddCurrencyRequest('Bitcoin'));

        $user = new User([
            'id' => 1,
            'name' => 'User1',
            'email' => 'user@example.com',
            'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret,
        ]);

        $wallet = new Wallet([
            'id' => 1,
            'user_id' => $user->id
        ]);

        $money = new Money([
            'id' => 1,
            'wallet_id' => $wallet->id,
            'currency_id' => $currency->id,
            'amount' => 500
        ]);

        $lot = new Lot([
            'id' => 1,
            'currency_id' => $currency->id,
            'seller_id' => $user->id,
            'date_time_open' => Carbon::now(),
            'date_time_close' => Carbon::tomorrow(),
            'price' => 5
        ]);

        $this->lotRepository->method('getById')->willReturn($lot);
        $this->currencyRepository->method('getById')->willReturn($currency);
        $this->userRepository->method('getById')->willReturn($user);
        $this->walletRepository->method('findByUser')->willReturn($wallet);
        $this->moneyRepository->method('findByWalletAndCurrency')->willReturn($money);
        $response = $this->marketService->getLot($lot->id);
        $this->assertEquals($lot->id, $response->getId());
        $this->assertEquals($user->name, $response->getUserName());
        $this->assertEquals($currency->name, $response->getCurrencyName());
        $this->assertEquals($lot->date_time_open->format('Y/m/d H:i:s'), $response->getDateTimeOpen());
        $this->assertEquals($lot->date_time_close->format('Y/m/d H:i:s'), $response->getDateTimeClose());
        $this->assertEquals($lot->price, $response->getPrice());
    }

    public function testGetLotList()
    {
        $currency = $this->currencyService->addCurrency(new AddCurrencyRequest('Bitcoin'));

        $user = new User([
            'id' => 1,
            'name' => 'User1',
            'email' => 'user@example.com',
            'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret,
        ]);

        $wallet = new Wallet([
            'id' => 1,
            'user_id' => $user->id
        ]);

        $money = new Money([
            'id' => 1,
            'wallet_id' => $wallet->id,
            'currency_id' => $currency->id,
            'amount' => 500
        ]);

        $lot = new Lot([
            'id' => 1,
            'currency_id' => $currency->id,
            'seller_id' => $user->id,
            'date_time_open' => Carbon::now(),
            'date_time_close' => Carbon::tomorrow(),
            'price' => 5
        ]);

        $this->lotRepository->method('getById')->willReturn($lot);
        $this->currencyRepository->method('getById')->willReturn($currency);
        $this->userRepository->method('getById')->willReturn($user);
        $this->walletRepository->method('findByUser')->willReturn($wallet);
        $this->moneyRepository->method('findByWalletAndCurrency')->willReturn($money);
        $this->lotRepository->method('findAll')->willReturn([$lot, $lot]);
        $lotList = $this->marketService->getLotList();
        foreach ($lotList as $lot) {
            $this->assertInstanceOf(LotResponse::class, $lot);
        }
    }

    private static function returnModelWithId(Model $model)
    {
        $model->id = random_int(1, 100);
        return $model;
    }
}
