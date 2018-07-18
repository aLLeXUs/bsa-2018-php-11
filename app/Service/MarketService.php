<?php

namespace App\Service;

use App\Entity\Lot;
use App\Entity\Trade;
use App\Request\Contracts\AddLotRequest;
use App\Request\Contracts\BuyLotRequest;
use App\Response\Contracts\LotResponse;

class MarketService implements Contracts\MarketService
{
    public function addLot(AddLotRequest $lotRequest): Lot
    {
        // TODO: Implement addLot() method.
    }

    public function buyLot(BuyLotRequest $lotRequest): Trade
    {
        // TODO: Implement buyLot() method.
    }

    public function getLot(int $id): LotResponse
    {
        // TODO: Implement getLot() method.
    }

    public function getLotList(): array
    {
        // TODO: Implement getLotList() method.
    }
}