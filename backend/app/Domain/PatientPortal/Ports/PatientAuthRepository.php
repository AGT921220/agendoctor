<?php

namespace App\Domain\PatientPortal\Ports;

interface PatientAuthRepository
{
    /**
     * Crea o refresca la invitación y retorna el token en texto plano.
     */
    public function createOrRefreshInvitation(int $patientId, string $email, \DateTimeImmutable $expiresAt, string $tokenPlain): void;

    /**
     * Canjea token (si es válido). Retorna user_id y patient_id.
     *
     * @return array{user_id:int, patient_id:int}|null
     */
    public function redeemInvitationToken(string $tokenPlain, \DateTimeImmutable $now): ?array;

    /**
     * @return int|null patient_id for given user_id
     */
    public function findPatientIdByUserId(int $userId): ?int;
}

