<?php

namespace App\Domain\Patient\Ports;

use App\Domain\Patient\Entities\Patient;

interface PatientRepository
{
    public function findById(int $id): ?Patient;
}

