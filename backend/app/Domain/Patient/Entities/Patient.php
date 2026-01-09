<?php

namespace App\Domain\Patient\Entities;

final readonly class Patient
{
    public function __construct(
        public int $id,
        public string $firstName,
        public ?string $lastName,
        public ?string $email,
        public ?string $phone,
        public ?string $adminNotes,
    ) {
    }
}

