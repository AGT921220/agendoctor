<?php

namespace Tests\Feature\Agenda;

use App\Domain\Appointment\AppointmentStatus;
use App\Infrastructure\Persistence\Eloquent\Models\Appointment;
use App\Infrastructure\Persistence\Eloquent\Models\Patient;
use App\Infrastructure\Persistence\Eloquent\Models\PracticeHoliday;
use App\Infrastructure\Persistence\Eloquent\Models\PracticeScheduleBlock;
use App\Infrastructure\Persistence\Eloquent\Models\PracticeSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AvailableSlotsTest extends TestCase
{
    use RefreshDatabase;

    public function test_slots_respect_buffer_between_appointments(): void
    {
        PracticeSettings::query()->create([
            'timezone' => 'UTC',
            'default_appointment_duration_minutes' => 30,
            'buffer_minutes' => 10,
            'confirm_cancel_cutoff_hours' => 12,
        ]);

        // Lunes: 09:00 - 11:00
        PracticeScheduleBlock::query()->create([
            'day_of_week' => 1,
            'start_time' => '09:00:00',
            'end_time' => '11:00:00',
        ]);

        $patient = Patient::factory()->create();

        // Cita existente: 09:00 - 09:30 (buffer 10 => protege hasta 09:40)
        Appointment::query()->create([
            'patient_id' => $patient->id,
            'starts_at' => '2026-01-12 09:00:00', // Monday
            'duration_minutes' => 30,
            'status' => AppointmentStatus::scheduled->value,
            'reason' => null,
        ]);

        $response = $this->getJson('/api/v1/agenda/available-slots?date=2026-01-12&duration=30');
        $response->assertOk();

        $slots = $response->json('slots');
        $this->assertIsArray($slots);

        // 09:30 NO debe estar disponible por buffer (hasta 09:40)
        $this->assertFalse(in_array('2026-01-12T09:30:00+00:00', $slots, true));

        // 09:40 sí puede estar disponible (step 5 min => existe 09:40)
        $this->assertTrue(in_array('2026-01-12T09:40:00+00:00', $slots, true));
    }

    public function test_holiday_blocks_all_slots(): void
    {
        PracticeSettings::query()->create([
            'timezone' => 'UTC',
            'default_appointment_duration_minutes' => 30,
            'buffer_minutes' => 0,
            'confirm_cancel_cutoff_hours' => 12,
        ]);

        PracticeScheduleBlock::query()->create([
            'day_of_week' => 1,
            'start_time' => '09:00:00',
            'end_time' => '11:00:00',
        ]);

        PracticeHoliday::query()->create([
            'date' => '2026-01-12',
            'label' => 'Día inhábil',
        ]);

        $response = $this->getJson('/api/v1/agenda/available-slots?date=2026-01-12&duration=30');
        $response->assertOk();
        $response->assertJson([
            'slots' => [],
        ]);
    }
}

