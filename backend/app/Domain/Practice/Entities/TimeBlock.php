<?php

namespace App\Domain\Practice\Entities;

/**
 * Bloque de horario para un día de la semana.
 * dayOfWeek: 1 (Mon) ... 7 (Sun)
 * start/end: "HH:MM"
 */
final readonly class TimeBlock
{
    public function __construct(
        public int $dayOfWeek,
        public string $startTime,
        public string $endTime,
    ) {
    }
}

