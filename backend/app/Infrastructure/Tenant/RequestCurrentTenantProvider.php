<?php

namespace App\Infrastructure\Tenant;

use App\Domain\Tenant\Ports\CurrentTenantProvider;

/**
 * Estado por request: el middleware setea el tenant_id.
 */
final class RequestCurrentTenantProvider implements CurrentTenantProvider
{
    private ?int $tenantId = null;

    public function setTenantId(?int $tenantId): void
    {
        $this->tenantId = $tenantId;
    }

    public function tenantId(): ?int
    {
        return $this->tenantId;
    }
}

