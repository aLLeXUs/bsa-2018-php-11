<?php

namespace App\Repository;

use App\Entity\Lot;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class DatabaseLotRepository implements Contracts\LotRepository
{
    public function add(Lot $lot): Lot
    {
        $lot->save();
        return $lot;
    }

    public function getById(int $id): ?Lot
    {
        return Lot::find($id);
    }

    public function isActiveById(int $id): bool
    {
        $lot = Lot::find($id);
        if ($lot->getDateTimeOpen() <= Carbon::now()->getTimestamp() &&
            $lot->getDateTimeClose() > Carbon::now()->getTimestamp()) {
            return true;
        } else {
            return false;
        }
    }

    public function findAll()
    {
        return Lot::all();
    }

    public function findActiveLot(int $userId): ?Lot
    {
        return Lot::where('seller_id', $userId)
            ->whereDate('date_time_open', '<=', Carbon::now())
            ->whereDate('date_time_close', '>', Carbon::now())->first();
    }

    public function findAllActiveLots(int $userId): Collection
    {
        return Lot::where('seller_id', $userId)
            ->whereDate('date_time_open', '<=', Carbon::now())
            ->whereDate('date_time_close', '>', Carbon::now())->get();
    }
}