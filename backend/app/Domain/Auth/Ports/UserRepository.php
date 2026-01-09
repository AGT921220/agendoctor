<?php

namespace App\Domain\Auth\Ports;

use App\Domain\Auth\Entities\User;
use App\Domain\Auth\Entities\UserAuthRecord;

interface UserRepository
{
    public function findAuthByEmail(string $email): ?UserAuthRecord;

    public function findById(int $id): ?User;
}

