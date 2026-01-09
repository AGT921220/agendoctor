<?php

namespace App\Domain\Auth\Entities;

use App\Domain\Auth\Role;

final readonly class User
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public Role $role,
    ) {
    }
}

