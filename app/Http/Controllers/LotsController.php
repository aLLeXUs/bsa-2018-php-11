<?php

namespace App\Http\Controllers;

use App\Entity\Currency;
use App\Entity\Money;
use App\Request\AddLotRequest;
use App\Service\MarketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LotsController extends Controller
{
    private $marketService;

    public function __construct(MarketService $marketService)
    {
        $this->marketService = $marketService;
    }

    public function create()
    {
        $currencies = Currency::rightJoin('money', 'money.currency_id', '=', 'currencies.id')
            ->select('currencies.id', 'currencies.name')
            ->get();
        return view('lot_create', ['currencies' => $currencies]);
    }

    public function store(Request $request)
    {
        try {
            $addLotRequest = new AddLotRequest(
                (int)$request->input('currency_id'),
                Auth::id(),
                (new Carbon($request->input('date_time_open')))->getTimestamp(),
                (new Carbon($request->input('date_time_close')))->getTimestamp(),
                (float)$request->input('price')
            );
            $this->marketService->addLot($addLotRequest);
            return 'Lot has been added successfully!';
        } catch (\Exception $exception) {
            return 'Sorry, error has been occurred: incorrect input';
        }
    }
}
