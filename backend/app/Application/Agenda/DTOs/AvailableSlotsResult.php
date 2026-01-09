<?php

namespace App\Application\Agenda\DTOs;

final readonly class AvailableSlotsResult
{
    /**
     * @param  list<string>  $slots  ISO8601 local (timezone del consultorio)
     */
    public function __construct(
        public string $date,
        public string $timezone,
        public int $durationMinutes,
        public array $slots,
    ) {
    }
}

