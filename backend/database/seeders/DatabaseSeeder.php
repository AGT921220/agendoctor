<?php

namespace Database\Seeders;

use App\Domain\Auth\Role;
use App\Infrastructure\Persistence\Eloquent\Models\Tenant;
use App\Infrastructure\Persistence\Eloquent\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $tenant = Tenant::query()->create([
            'name' => 'Clínica Demo',
        ]);

        User::query()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Admin',
            'email' => 'admin@demo.test',
            'password' => Hash::make('password'),
            'role' => Role::TENANT_ADMIN->value,
        ]);

        User::query()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Doctor',
            'email' => 'doctor@demo.test',
            'password' => Hash::make('password'),
            'role' => Role::DOCTOR->value,
        ]);

        User::query()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Recepción',
            'email' => 'reception@demo.test',
            'password' => Hash::make('password'),
            'role' => Role::RECEPTIONIST->value,
        ]);
    }
}
