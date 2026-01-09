<?php

namespace App\Application\Auth\UseCases;

use App\Domain\Auth\Ports\TokenService;

final readonly class LogoutUseCase
{
    public function __construct(
        private TokenService $tokens,
    ) {
    }

    public function execute(): void
    {
        $this->tokens->revokeCurrentToken();
    }
}

