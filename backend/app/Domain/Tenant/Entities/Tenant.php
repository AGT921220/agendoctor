<?php

namespace App\Domain\Tenant\Entities;

final readonly class Tenant
{
    public function __construct(
        public int $id,
        public string $name,
    ) {
    }
}

