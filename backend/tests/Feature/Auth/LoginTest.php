<?php

namespace Tests\Feature\Auth;

use App\Infrastructure\Persistence\Eloquent\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_and_receive_token(): void
    {
        User::query()->create([
            'name' => 'Admin',
            'email' => 'admin@demo.test',
            'password' => Hash::make('password'),
            'role' => \App\Domain\Auth\Role::ADMIN->value,
        ]);

        $response = $this->postJson('/api/v1/login', [
            'email' => 'admin@demo.test',
            'password' => 'password',
            'device_name' => 'tests',
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'token',
            'user' => ['id', 'name', 'email', 'role'],
        ]);
        $this->assertIsString($response->json('token'));
        $this->assertNotEmpty($response->json('token'));
    }

    public function test_invalid_login_returns_json_error_with_trace_id(): void
    {
        $response = $this->postJson('/api/v1/login', [
            'email' => 'nobody@demo.test',
            'password' => 'wrongpass',
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message',
            'errors',
            'trace_id',
        ]);
    }
}

