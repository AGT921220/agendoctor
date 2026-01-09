<?php

namespace App\Domain\Tenant\Ports;

interface CurrentTenantProvider
{
    public function tenantId(): ?int;
}

