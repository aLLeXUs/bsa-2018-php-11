<?php

namespace App\Repository;

use App\User;

class DatabaseUserRepository implements Contracts\UserRepository
{
    public function getById(int $id): ?User
    {
        // TODO: Implement getById() method.
    }
}