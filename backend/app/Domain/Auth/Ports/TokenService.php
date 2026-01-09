<?php

namespace App\Domain\Auth\Ports;

interface TokenService
{
    public function createTokenForUserId(int $userId, string $tokenName = 'api'): string;

    public function revokeCurrentToken(): void;
}

