<?php

namespace App\Infrastructure\Auth;

use App\Domain\Auth\Ports\TokenService;
use App\Infrastructure\Persistence\Eloquent\Models\User;
use Illuminate\Support\Facades\Auth;
use RuntimeException;

final class LaravelTokenService implements TokenService
{
    public function createTokenForUserId(int $userId, string $tokenName = 'api'): string
    {
        $user = User::query()->find($userId);

        if (! $user) {
            throw new RuntimeException('Usuario no encontrado.');
        }

        return $user->createToken($tokenName)->plainTextToken;
    }

    public function revokeCurrentToken(): void
    {
        $user = Auth::user();

        if (! $user || ! method_exists($user, 'currentAccessToken')) {
            return;
        }

        $token = $user->currentAccessToken();
        $token?->delete();
    }
}

