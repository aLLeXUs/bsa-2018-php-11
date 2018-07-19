<?php

namespace App\Service;

use App\Entity\Lot;
use App\Entity\Trade;
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
        // TODO
        $lot = new Lot([
            'currency_id' => $lotRequest->getCurrencyId(),
            'seller_id' => $lotRequest->getSellerId(),
            'date_time_open' => $lotRequest->getDateTimeOpen(),
            'date_time_close' => $lotRequest->getDateTimeClose(),
            'price' => $lotRequest->getPrice(),
        ]);
        $this->lotRepository->add($lot);
    }

    public function buyLot(BuyLotRequest $lotRequest): Trade
    {
        $lot = $this->lotRepository->findActiveLot($lotRequest->getLotId());
        $buyer = $this->userRepository->getById($lotRequest->getUserId());
        $seller = $this->userRepository->getById($lot->seller_id);
        $buyerWallet = $this->walletRepository->findByUser($buyer->id);
        $sellerWallet = $this->walletRepository->findByUser($seller->id);
        $buyerMoney = $this->moneyRepository->findByWalletAndCurrency($buyerWallet->id, $lot->currency_id);
        $sellerMoney = $this->moneyRepository->findByWalletAndCurrency($sellerWallet->id, $lot->currency_id);
        if ($seller->id != $buyer->id &&
            $lotRequest->getAmount() <= $buyerMoney->amount &&
            $lotRequest->getAmount() >= 1) {
            $this->walletService->takeMoney(new MoneyRequest($buyerWallet->id, $lot->currency_id,
                $lotRequest->getAmount()));
            $this->walletService->addMoney(new MoneyRequest($sellerWallet->id, $lot->currnncy_id,
                $lotRequest->getAmount()));
            $trade = new Trade([
                'lot_id' => $lotRequest->getLotId(),
                'user_id' => $buyer->id,
                'amount' => $lotRequest->getAmount(),
            ]);
            $this->tradeRepository->add($trade);
            Mail::send(new TradeCreated($trade));
        }
    }

    public function getLot(int $id): LotResponse
    {
        $lot = $this->lotRepository->getById($id);
        $currency = $this->currencyRepository->getById($lot->currency_id);
        $user = $this->userRepository->getById($lot->user_id);
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