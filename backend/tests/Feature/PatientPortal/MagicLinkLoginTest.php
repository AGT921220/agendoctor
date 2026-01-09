<?php

namespace Tests\Feature\PatientPortal;

use App\Infrastructure\Persistence\Eloquent\Models\Patient;
use App\Infrastructure\Persistence\Eloquent\Models\PatientAuth;
use DateTimeImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class MagicLinkLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_magic_link_expires_and_is_not_reusable(): void
    {
        $patient = Patient::factory()->create([
            'email' => 'patient@demo.test',
        ]);

        // Crear invitación manual (simula InvitePatientToPortal)
        $plain = Str::random(64);
        $hash = hash('sha256', $plain);

        PatientAuth::query()->create([
            'patient_id' => $patient->id,
            'user_id' => null,
            'invitation_status' => 'pending',
            'invitation_token_hash' => $hash,
            'invitation_expires_at' => (new DateTimeImmutable('now'))->modify('-1 minute')->format('Y-m-d H:i:s'),
            'invited_at' => (new DateTimeImmutable('now'))->format('Y-m-d H:i:s'),
        ]);

        // Token expirado => 422 JSON error
        $expired = $this->postJson('/api/v1/patient/magic-link-login', ['token' => $plain]);
        $expired->assertStatus(422);
        $expired->assertJsonStructure(['message', 'errors', 'trace_id']);

        // Ahora invitación válida + no reutilizable
        $plain2 = Str::random(64);
        $hash2 = hash('sha256', $plain2);

        PatientAuth::query()->where('patient_id', $patient->id)->update([
            'invitation_status' => 'pending',
            'invitation_token_hash' => $hash2,
            'invitation_expires_at' => (new DateTimeImmutable('now'))->modify('+10 minutes')->format('Y-m-d H:i:s'),
        ]);

        // Como el repo crea user en Invite use case, aquí forzamos user_id creando invitación desde API
        // para tener user vinculada: usamos endpoint invite.
        // Creamos un usuario staff y lo autenticamos para invitar.
        $staff = \App\Infrastructure\Persistence\Eloquent\Models\User::query()->create([
            'name' => 'Admin',
            'email' => 'admin@demo.test',
            'password' => Hash::make('password'),
            'role' => \App\Domain\Auth\Role::ADMIN->value,
        ]);

        $staffToken = $staff->createToken('tests')->plainTextToken;

        $invite = $this->postJson(
            "/api/v1/patients/{$patient->id}/invite",
            ['ttl_minutes' => 10],
            ['Authorization' => "Bearer {$staffToken}"],
        );

        $invite->assertOk();
        $token = (string) $invite->json('token');

        $login1 = $this->postJson('/api/v1/patient/magic-link-login', ['token' => $token]);
        $login1->assertOk();
        $login1->assertJsonStructure(['token']);

        // Reutilizar el mismo token => inválido
        $login2 = $this->postJson('/api/v1/patient/magic-link-login', ['token' => $token]);
        $login2->assertStatus(422);
        $login2->assertJsonStructure(['message', 'errors', 'trace_id']);
    }
}

