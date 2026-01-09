<?php

namespace App\Domain\Appointment\Entities;

use App\Domain\Appointment\AppointmentStatus;

final readonly class Appointment
{
    public function __construct(
        public int $id,
        public int $patientId,
        public \DateTimeImmutable $startsAt,
        public int $durationMinutes,
        public AppointmentStatus $status,
        public ?string $reason,
    ) {
    }
}

