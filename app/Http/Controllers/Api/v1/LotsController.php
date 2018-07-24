<?php

namespace App\Http\Controllers\Api\v1;

use App\Exceptions\MarketException\LotDoesNotExistException;
use App\Request\AddLotRequest;
use App\Request\BuyLotRequest;
use App\Service\Contracts\MarketService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LotsController extends Controller
{
    private $marketService;

    public function __construct(MarketService $marketService)
    {
        $this->marketService = $marketService;
    }

    public function index()
    {
        $lotList = $this->marketService->getLotList();
        $lotResponse = [];
        foreach ($lotList as $lot) {
            $lotResponse[] = [
                'id' => $lot->getId(),
                'user_name' => $lot->getUserName(),
                'currency_name' => $lot->getCurrencyName(),
                'amount' => $lot->getAmount(),
                'date_time_open' => $lot->getDateTimeOpen(),
                'date_time_close' => $lot->getDateTimeClose(),
                'price' => $lot->getPrice()
            ];
        }
        return response()->json($lotResponse);
    }

    public function store(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'error' => [
                    'message' => 'Only authorised users can add lots',
                    'code' => 403
                ]
            ], 403);
        }
        try {
            $addLotRequest = new AddLotRequest(
                (int)$request->input('currency_id'),
                Auth::id(),
                (int)$request->input('date_time_open'),
                (int)$request->input('date_time_close'),
                (float)$request->input('price')
            );
            return response()->json($this->marketService->addLot($addLotRequest), 201);
        } catch (\LogicException $exception) {
            return response()->json([
                'error' => [
                    'message' => $exception->getMessage(),
                    'code' => 400
                ]
            ], 400);
        }
    }

    public function show($id)
    {
        try {
            $lot = $this->marketService->getLot($id);
            return response()->json([
                'id' => $lot->getId(),
                'user_name' => $lot->getUserName(),
                'currency_name' => $lot->getCurrencyName(),
                'amount' => $lot->getAmount(),
                'date_time_open' => $lot->getDateTimeOpen(),
                'date_time_close' => $lot->getDateTimeClose(),
                'price' => $lot->getPrice()
            ]);
        } catch (LotDoesNotExistException $exception) {
            return response()->json([
                'error' => [
                    'message' => 'Lot does not exist',
                    'code' => 404
                ]
            ], 404);
        }
    }

    public function buy(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'error' => [
                    'message' => 'Only authorised users can buy lots',
                    'code' => 403
                ]
            ], 403);
        }
            $buyLotRequest = new BuyLotRequest(
                Auth::id(),
                (int)$request->input('lot_id'),
                (float)$request->input('amount')
            );
            return response()->json($this->marketService->buyLot($buyLotRequest), 201);
    }
}
