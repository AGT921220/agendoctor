<?php

namespace Database\Seeders;

use App\Domain\Auth\Role;
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
        User::query()->create([
            'name' => 'Admin',
            'email' => 'admin@demo.test',
            'password' => Hash::make('password'),
            'role' => Role::ADMIN->value,
        ]);

        User::query()->create([
            'name' => 'Recepción',
            'email' => 'reception@demo.test',
            'password' => Hash::make('password'),
            'role' => Role::RECEPTIONIST->value,
        ]);
    }
}
