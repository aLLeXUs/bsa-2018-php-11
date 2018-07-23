<?php

namespace App\Service;

use App\Entity\Lot;
use App\Entity\Trade;
use App\Exceptions\MarketException\ActiveLotExistsException;
use App\Exceptions\MarketException\BuyInactiveLotException;
use App\Exceptions\MarketException\BuyNegativeAmountException;
use App\Exceptions\MarketException\BuyOwnCurrencyException;
use App\Exceptions\MarketException\IncorrectLotAmountException;
use App\Exceptions\MarketException\IncorrectPriceException;
use App\Exceptions\MarketException\IncorrectTimeCloseException;
use App\Exceptions\MarketException\LotDoesNotExistException;
use App\Mail\TradeCreated;
use App\Repository\Contracts\CurrencyRepository;
use App\Repository\Contracts\LotRepository;
use App\Repository\Contracts\MoneyRepository;
use App\Repository\Contracts\TradeRepository;
use App\Repository\Contracts\UserRepository;
use App\Repository\Contracts\WalletRepository;
use App\Request\Contracts\AddLotRequest;
use App\Request\Contracts\BuyLotRequest;
use App\Request\MoneyRequest;
use App\Response\Contracts\LotResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class MarketService implements Contracts\MarketService
{
    private $lotRepository;
    private $currencyRepository;
    private $userRepository;
    private $walletRepository;
    private $moneyRepository;
    private $tradeRepository;
    private $walletService;

    public function __construct(LotRepository $lotRepository, CurrencyRepository $currencyRepository,
                                UserRepository $userRepository, WalletRepository $walletRepository,
                                MoneyRepository $moneyRepository, TradeRepository $tradeRepository,
                                \App\Service\Contracts\WalletService $walletService)
    {
        $this->lotRepository = $lotRepository;
        $this->currencyRepository = $currencyRepository;
        $this->userRepository = $userRepository;
        $this->walletRepository = $walletRepository;
        $this->moneyRepository = $moneyRepository;
        $this->tradeRepository = $tradeRepository;
        $this->walletService = $walletService;
    }

    public function addLot(AddLotRequest $lotRequest): Lot
    {
        $lots = $this->lotRepository->findAllActiveLots($lotRequest->getSellerId());
        foreach ($lots as $lot) {
            if ($lot->currency_id == $lotRequest->getCurrencyId()) {
                throw new ActiveLotExistsException();
            }
        }
        if ($lotRequest->getDateTimeClose() <= $lotRequest->getDateTimeOpen()) {
            throw new IncorrectTimeCloseException();
        }
        if ($lotRequest->getPrice() < 0) {
            throw new IncorrectPriceException();
        }
        $lot = new Lot([
            'currency_id' => $lotRequest->getCurrencyId(),
            'seller_id' => $lotRequest->getSellerId(),
            'date_time_open' => Carbon::createFromTimestamp($lotRequest->getDateTimeOpen()),
            'date_time_close' => Carbon::createFromTimestamp($lotRequest->getDateTimeClose()),
            'price' => $lotRequest->getPrice(),
        ]);
        return $this->lotRepository->add($lot);
    }

    public function buyLot(BuyLotRequest $lotRequest): Trade
    {
        $lot = $this->lotRepository->findActiveLot($lotRequest->getLotId());
        $buyer = $this->userRepository->getById($lotRequest->getUserId());
        $seller = $this->userRepository->getById($lot->seller_id);
        $buyerWallet = $this->walletRepository->findByUser($buyer->id);
        $sellerWallet = $this->walletRepository->findByUser($seller->id);
        $sellerMoney = $this->moneyRepository->findByWalletAndCurrency($sellerWallet->id, $lot->currency_id);
        if ($seller->id != $buyer->id) {
            throw new BuyOwnCurrencyException();
        }
        if ($lotRequest->getAmount() > $sellerMoney->amount) {
            throw new IncorrectLotAmountException();
        }
        if ($lotRequest->getAmount() < 1) {
            throw new BuyNegativeAmountException();
        }
        if (!$this->lotRepository->isActiveById($lotRequest->getLotId())) {
            throw new BuyInactiveLotException();
        }
        $this->walletService->takeMoney(new MoneyRequest($buyerWallet->id, $lot->currency_id,
            $lotRequest->getAmount()));
        $this->walletService->addMoney(new MoneyRequest($sellerWallet->id, $lot->currency_id,
            $lotRequest->getAmount()));
        $trade = new Trade([
            'lot_id' => $lotRequest->getLotId(),
            'user_id' => $buyer->id,
            'amount' => $lotRequest->getAmount(),
        ]);
        $this->tradeRepository->add($trade);
        Mail::send(new TradeCreated($trade));
        return $trade;
    }

    public function getLot(int $id): LotResponse
    {
        $lot = $this->lotRepository->getById($id);
        if (empty($lot)) {
            throw new LotDoesNotExistException();
        }
        $currency = $this->currencyRepository->getById($lot->currency_id);
        $user = $this->userRepository->getById($lot->seller_id);
        $wallet = $this->walletRepository->findByUser($user->id);
        $money = $this->moneyRepository->findByWalletAndCurrency($wallet->id, $currency->id);
        $response = new \App\Response\LotResponse($lot->id, $user->name, $currency->name,
            $money->amount, $lot->getDateTimeOpen(), $lot->getDateTimeClose(), $lot->price);
        return $response;
    }

    public function getLotList(): array
    {
        $list = [];
        $lots = $this->lotRepository->findAll();
        foreach ($lots as $lot) {
            $list[] = $this->getLot($lot->id);
        }
        return $list;
    }
}