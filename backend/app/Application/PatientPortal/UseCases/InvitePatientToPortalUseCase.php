<?php

namespace App\Application\PatientPortal\UseCases;

use App\Domain\Patient\Ports\PatientRepository;
use App\Domain\PatientPortal\Ports\PatientAuthRepository;
use DateTimeImmutable;
use Illuminate\Support\Str;
use RuntimeException;

final readonly class InvitePatientToPortalUseCase
{
    public function __construct(
        private PatientRepository $patients,
        private PatientAuthRepository $patientAuth,
    ) {
    }

    /**
     * @return array{token: string, expires_at: string}
     */
    public function execute(int $patientId, int $ttlMinutes = 60 * 24): array
    {
        $patient = $this->patients->findById($patientId);

        if (! $patient) {
            throw new RuntimeException('Paciente no encontrado.');
        }

        if (! $patient->email) {
            throw new RuntimeException('El paciente no tiene email.');
        }

        $token = Str::random(64);
        $expiresAt = (new DateTimeImmutable('now'))->modify('+'.$ttlMinutes.' minutes');

        $this->patientAuth->createOrRefreshInvitation(
            patientId: $patient->id,
            email: $patient->email,
            expiresAt: $expiresAt,
            tokenPlain: $token,
        );

        return [
            'token' => $token,
            'expires_at' => $expiresAt->format(DATE_ATOM),
        ];
    }
}

