<?php

namespace App\Domain\Auth\Entities;

use App\Domain\Auth\Role;

/**
 * Datos mínimos para autenticar (incluye password hash).
 * No es un modelo Eloquent.
 */
final readonly class UserAuthRecord
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public Role $role,
        public string $passwordHash,
    ) {
    }
}

