<?php

namespace App\Application\Auth\UseCases;

use App\Application\Auth\Exceptions\InvalidCredentialsException;
use App\Domain\Auth\Entities\User;
use App\Domain\Auth\Ports\PasswordHasher;
use App\Domain\Auth\Ports\TokenService;
use App\Domain\Auth\Ports\UserRepository;

final readonly class LoginUseCase
{
    public function __construct(
        private UserRepository $users,
        private PasswordHasher $hasher,
        private TokenService $tokens,
    ) {
    }

    /**
     * @return array{token: string, user: User}
     */
    public function execute(string $email, string $password, string $tokenName = 'api'): array
    {
        $record = $this->users->findAuthByEmail($email);

        if (! $record || ! $this->hasher->check($password, $record->passwordHash)) {
            throw new InvalidCredentialsException('Credenciales inválidas.');
        }

        $token = $this->tokens->createTokenForUserId($record->id, $tokenName);

        return [
            'token' => $token,
            'user' => new User(
                id: $record->id,
                tenantId: $record->tenantId,
                name: $record->name,
                email: $record->email,
                role: $record->role,
            ),
        ];
    }
}

