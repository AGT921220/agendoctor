<?php

namespace App\Domain\Auth\Ports;

interface PasswordHasher
{
    public function check(string $plain, string $hash): bool;
}

