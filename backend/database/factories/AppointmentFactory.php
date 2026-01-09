<?php

namespace Database\Factories;

use App\Domain\Appointment\AppointmentStatus;
use App\Infrastructure\Persistence\Eloquent\Models\Appointment;
use App\Infrastructure\Persistence\Eloquent\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Appointment>
 */
class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'starts_at' => now()->addDay(),
            'duration_minutes' => 30,
            'status' => AppointmentStatus::scheduled->value,
            'reason' => fake()->sentence(3),
        ];
    }
}

