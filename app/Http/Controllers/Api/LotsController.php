<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\MarketException\LotDoesNotExistException;
use App\Request\Contracts\AddLotRequest;
use App\Request\Contracts\BuyLotRequest;
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
        return response()->json($this->marketService->getLotList());
    }

    public function store(AddLotRequest $request)
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
            return response()->json($this->marketService->addLot($request), 201);
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
            return response()->json($this->marketService->getLot($id));
        } catch (LotDoesNotExistException $exception) {
            return response()->json([
                'error' => [
                    'message' => 'Lot does not exist',
                    'code' => 404
                ]
            ], 404);
        }
    }

    public function buy(BuyLotRequest $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'error' => [
                    'message' => 'Only authorised users can buy lots',
                    'code' => 403
                ]
            ], 403);
        }
        try {
            return response()->json($this->marketService->buyLot($request), 201);
        } catch (\LogicException $exception) {
            return response()->json([
                'error' => [
                    'message' => $exception->getMessage(),
                    'code' => 400
                ]
            ], 400);
        }
    }
}
