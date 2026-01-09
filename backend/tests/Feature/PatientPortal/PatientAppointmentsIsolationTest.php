<?php

namespace Tests\Feature\PatientPortal;

use App\Domain\Appointment\AppointmentStatus;
use App\Infrastructure\Persistence\Eloquent\Models\Appointment;
use App\Infrastructure\Persistence\Eloquent\Models\Patient;
use App\Infrastructure\Persistence\Eloquent\Models\PatientAuth;
use App\Infrastructure\Persistence\Eloquent\Models\Subscription;
use App\Infrastructure\Persistence\Eloquent\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PatientAppointmentsIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_patient_can_only_see_their_appointments(): void
    {
        // Para poder invitar, el sistema debe estar activo (no read-only).
        Subscription::query()->create(['status' => 'active', 'plan_key' => 'BASIC']);

        $patientA = Patient::factory()->create(['email' => 'a@demo.test']);
        $patientB = Patient::factory()->create(['email' => 'b@demo.test']);

        Appointment::query()->create([
            'patient_id' => $patientA->id,
            'starts_at' => '2026-01-12 10:00:00',
            'duration_minutes' => 30,
            'status' => AppointmentStatus::scheduled->value,
            'reason' => 'A',
        ]);

        Appointment::query()->create([
            'patient_id' => $patientB->id,
            'starts_at' => '2026-01-12 11:00:00',
            'duration_minutes' => 30,
            'status' => AppointmentStatus::scheduled->value,
            'reason' => 'B',
        ]);

        // Invitar a paciente A y hacer magic link login para obtener token.
        $staff = User::query()->create([
            'name' => 'Admin',
            'email' => 'admin@demo.test',
            'password' => Hash::make('password'),
            'role' => \App\Domain\Auth\Role::ADMIN->value,
        ]);
        $staffToken = $staff->createToken('tests')->plainTextToken;

        $invite = $this->postJson(
            "/api/v1/patients/{$patientA->id}/invite",
            ['ttl_minutes' => 10],
            ['Authorization' => "Bearer {$staffToken}"],
        )->assertOk();

        $magicToken = (string) $invite->json('token');

        $link = PatientAuth::query()->where('patient_id', $patientA->id)->first();
        $this->assertNotNull($link);
        $this->assertNotNull($link->user_id);
        $patientUser = User::query()->where('email', $patientA->email)->first();
        $this->assertNotNull($patientUser);
        $this->assertSame((int) $patientUser->id, (int) $link->user_id);

        $login = $this->postJson('/api/v1/patient/magic-link-login', ['token' => $magicToken])
            ->assertOk();

        $patientApiToken = (string) $login->json('token');
        $this->assertSame((int) $patientUser->id, (int) $login->json('user_id'));
        $this->assertSame((int) $patientA->id, (int) $login->json('patient_id'));

        $resp = $this->getJson(
            '/api/v1/patient/appointments',
            ['Authorization' => "Bearer {$patientApiToken}"],
        )->assertOk();

        $payload = $resp->json();
        $this->assertArrayHasKey('upcoming', $payload);
        $this->assertArrayHasKey('history', $payload);

        $all = array_merge($payload['upcoming'], $payload['history']);
        $this->assertCount(1, $all);
        $this->assertSame('A', $all[0]['reason']);
    }
}

