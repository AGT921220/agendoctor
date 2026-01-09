<?php

namespace Tests\Feature\Tenant;

use App\Domain\Auth\Role;
use App\Infrastructure\Persistence\Eloquent\Models\Tenant;
use App\Infrastructure\Persistence\Eloquent\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TenantTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_get_current_tenant(): void
    {
        $tenant = Tenant::query()->create(['name' => 'Clínica Demo']);

        User::query()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Doctor',
            'email' => 'doctor@demo.test',
            'password' => Hash::make('password'),
            'role' => Role::DOCTOR->value,
        ]);

        $login = $this->postJson('/api/v1/login', [
            'email' => 'doctor@demo.test',
            'password' => 'password',
        ])->assertOk();

        $token = $login->json('token');

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/tenant');

        $response->assertOk();
        $response->assertJson([
            'tenant' => [
                'id' => $tenant->id,
                'name' => 'Clínica Demo',
            ],
        ]);
    }

    public function test_unauthenticated_tenant_request_returns_json_error(): void
    {
        $response = $this->getJson('/api/v1/tenant');

        $response->assertStatus(401);
        $response->assertJsonStructure([
            'message',
            'errors',
            'trace_id',
        ]);
    }
}

