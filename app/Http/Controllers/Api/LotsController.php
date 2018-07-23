<?php

namespace App\Http\Controllers\Api;

use App\Request\Contracts\AddLotRequest;
use App\Request\Contracts\BuyLotRequest;
use App\Service\Contracts\MarketService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
        return response()->json($this->marketService->addLot($request), 201);
    }

    public function show($id)
    {
        return response()->json($this->marketService->getLot($id));
    }

    public function buy(BuyLotRequest $request)
    {
        return response()->json($this->marketService->buyLot($request));
    }
}
