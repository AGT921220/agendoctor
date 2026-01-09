<?php

namespace App\Domain\Appointment\Ports;

use App\Domain\Appointment\Entities\Appointment;

interface AppointmentRepository
{
    /**
     * @return list<Appointment>
     */
    public function listBetween(\DateTimeImmutable $fromInclusive, \DateTimeImmutable $toExclusive): array;

    /**
     * @return list<Appointment>
     */
    public function listByPatientBetween(int $patientId, \DateTimeImmutable $fromInclusive, \DateTimeImmutable $toExclusive): array;

    public function findById(int $id): ?Appointment;

    public function updateStatus(int $id, string $status): void;
}

