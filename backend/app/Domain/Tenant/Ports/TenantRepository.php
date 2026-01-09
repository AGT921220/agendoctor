<?php

namespace App\Domain\Tenant\Ports;

use App\Domain\Tenant\Entities\Tenant;

interface TenantRepository
{
    public function findById(int $id): ?Tenant;
}

