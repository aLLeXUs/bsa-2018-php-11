<?php

namespace App\Service;

use App\Entity\Money;
use App\Entity\Wallet;
use App\Repository\Contracts\MoneyRepository;
use App\Repository\Contracts\WalletRepository;
use App\Request\Contracts\CreateWalletRequest;
use App\Request\Contracts\MoneyRequest;

class WalletService implements Contracts\WalletService
{
    private $moneyRepository;
    private $walletRepository;

    public function __construct(MoneyRepository $moneyRepository, WalletRepository $walletRepository)
    {
        $this->moneyRepository = $moneyRepository;
        $this->walletRepository = $walletRepository;
    }

    public function addWallet(CreateWalletRequest $walletRequest): Wallet
    {
        $wallet = $this->walletRepository->findByUser($walletRequest->getUserId());
        if (empty($wallet)) {
            $wallet = $this->walletRepository->add(new Wallet([
                'user_id' => $walletRequest->getUserId(),
            ]));
        }
        return $wallet;
    }

    public function addMoney(MoneyRequest $moneyRequest): Money
    {
        $money = $this->moneyRepository->findByWalletAndCurrency($moneyRequest->getWalletId(),
            $moneyRequest->getCurrencyId());
        if (empty($money)) {
            $money = new Money([
                'wallet_id' => $moneyRequest->getWalletId(),
                'currency_id' => $moneyRequest->getCurrencyId(),
                'amount' => 0,
            ]);
        }
        $money->amount += $moneyRequest->getAmount();
        return $this->moneyRepository->save($money);
    }

    public function takeMoney(MoneyRequest $moneyRequest): Money
    {
        $money = $this->moneyRepository->findByWalletAndCurrency($moneyRequest->getWalletId(),
            $moneyRequest->getCurrencyId());
        if ($money->amount >= $moneyRequest->getAmount()) {
            $money->amount -= $moneyRequest->getAmount();
        }
        return $money;
    }
}