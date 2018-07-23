<?php

namespace App\Repository\Contracts;

use App\Entity\Lot;
use Illuminate\Database\Eloquent\Collection;

interface LotRepository
{
    public function add(Lot $lot) : Lot;

    public function getById(int $id) : ?Lot;

    public function isActiveById(int $id): bool;
    /**
     * @return Lot[]
     */
    public function findAll();

    public function findActiveLot(int $userId) : ?Lot;

    public function findAllActiveLots(int $userId): Collection;
}