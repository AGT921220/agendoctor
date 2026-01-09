<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Practice\Entities\PracticeSettings as PracticeSettingsEntity;
use App\Domain\Practice\Entities\TimeBlock;
use App\Domain\Practice\Ports\PracticeSettingsRepository;
use App\Infrastructure\Persistence\Eloquent\Models\PracticeHoliday;
use App\Infrastructure\Persistence\Eloquent\Models\PracticeScheduleBlock;
use App\Infrastructure\Persistence\Eloquent\Models\PracticeSettings;

final class EloquentPracticeSettingsRepository implements PracticeSettingsRepository
{
    public function get(): PracticeSettingsEntity
    {
        $settings = PracticeSettings::query()->first();

        if (! $settings) {
            $settings = PracticeSettings::query()->create([
                'timezone' => 'America/Mexico_City',
                'default_appointment_duration_minutes' => 30,
                'buffer_minutes' => 0,
                'confirm_cancel_cutoff_hours' => 12,
            ]);
        }

        $blocks = PracticeScheduleBlock::query()
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get()
            ->map(fn ($b) => new TimeBlock(
                dayOfWeek: (int) $b->day_of_week,
                startTime: substr((string) $b->start_time, 0, 5),
                endTime: substr((string) $b->end_time, 0, 5),
            ))
            ->values()
            ->all();

        $holidays = PracticeHoliday::query()
            ->orderBy('date')
            ->pluck('date')
            ->map(fn ($d) => is_string($d) ? $d : (string) $d)
            ->values()
            ->all();

        return new PracticeSettingsEntity(
            timezone: (string) $settings->timezone,
            defaultAppointmentDurationMinutes: (int) $settings->default_appointment_duration_minutes,
            bufferMinutes: (int) $settings->buffer_minutes,
            confirmCancelCutoffHours: (int) $settings->confirm_cancel_cutoff_hours,
            scheduleBlocks: $blocks,
            holidays: $holidays,
        );
    }
}

