<?php

namespace App\Infrastructure\Auth;

use App\Domain\Auth\Ports\PasswordHasher;
use Illuminate\Support\Facades\Hash;

final class LaravelPasswordHasher implements PasswordHasher
{
    public function check(string $plain, string $hash): bool
    {
        return Hash::check($plain, $hash);
    }
}

