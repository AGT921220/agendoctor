<?php

namespace App\Application\PatientPortal\UseCases;

use App\Application\Auth\Exceptions\InvalidCredentialsException;
use App\Domain\Auth\Ports\TokenService;
use App\Domain\PatientPortal\Ports\PatientAuthRepository;
use DateTimeImmutable;

final readonly class PatientMagicLinkLoginUseCase
{
    public function __construct(
        private PatientAuthRepository $patientAuth,
        private TokenService $tokens,
    ) {
    }

    /**
     * @return array{token: string, patient_id: int, user_id: int}
     */
    public function execute(string $tokenPlain): array
    {
        $now = new DateTimeImmutable('now');

        $redeemed = $this->patientAuth->redeemInvitationToken($tokenPlain, $now);

        if (! $redeemed) {
            throw new InvalidCredentialsException('Token inválido o expirado.');
        }

        $apiToken = $this->tokens->createTokenForUserId($redeemed['user_id'], 'patient_portal');

        return [
            'token' => $apiToken,
            'patient_id' => $redeemed['patient_id'],
            'user_id' => $redeemed['user_id'],
        ];
    }
}

