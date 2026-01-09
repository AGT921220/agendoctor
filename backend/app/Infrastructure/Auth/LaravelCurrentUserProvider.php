<?php

namespace App\Infrastructure\Auth;

use App\Domain\Auth\Entities\User as UserEntity;
use App\Domain\Auth\Ports\CurrentUserProvider;
use App\Domain\Auth\Role;
use Illuminate\Support\Facades\Auth;

final class LaravelCurrentUserProvider implements CurrentUserProvider
{
    public function user(): ?UserEntity
    {
        $user = Auth::guard('sanctum')->user() ?? Auth::user();

        if (! $user) {
            return null;
        }

        return new UserEntity(
            id: (int) $user->id,
            name: (string) $user->name,
            email: (string) $user->email,
            role: Role::from((string) $user->role),
        );
    }
}

