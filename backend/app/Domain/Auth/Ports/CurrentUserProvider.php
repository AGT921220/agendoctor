<?php

namespace App\Domain\Auth\Ports;

use App\Domain\Auth\Entities\User;

interface CurrentUserProvider
{
    public function user(): ?User;
}

