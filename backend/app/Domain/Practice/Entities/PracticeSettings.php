<?php

namespace App\Domain\Practice\Entities;

final readonly class PracticeSettings
{
    /**
     * @param  list<TimeBlock>  $scheduleBlocks
     * @param  list<string>  $holidays  Fechas YYYY-MM-DD
     */
    public function __construct(
        public string $timezone,
        public int $defaultAppointmentDurationMinutes,
        public int $bufferMinutes,
        public int $confirmCancelCutoffHours,
        public array $scheduleBlocks,
        public array $holidays,
    ) {
    }
}

