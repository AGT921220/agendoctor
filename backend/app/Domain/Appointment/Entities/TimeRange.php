<?php

namespace App\Domain\Appointment\Entities;

final readonly class TimeRange
{
    public function __construct(
        public \DateTimeImmutable $start,
        public \DateTimeImmutable $end,
    ) {
    }
}

