<?php

namespace App\Application\Auth\UseCases;

use App\Domain\Auth\Entities\User;
use App\Domain\Auth\Ports\CurrentUserProvider;

final readonly class GetMeUseCase
{
    public function __construct(
        private CurrentUserProvider $currentUser,
    ) {
    }

    public function execute(): ?User
    {
        return $this->currentUser->user();
    }
}

