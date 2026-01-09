<?php

namespace App\Application\Tenant\UseCases;

use App\Domain\Tenant\Entities\Tenant;
use App\Domain\Tenant\Ports\CurrentTenantProvider;
use App\Domain\Tenant\Ports\TenantRepository;
use RuntimeException;

final readonly class GetCurrentTenantUseCase
{
    public function __construct(
        private CurrentTenantProvider $currentTenant,
        private TenantRepository $tenants,
    ) {
    }

    public function execute(): Tenant
    {
        $tenantId = $this->currentTenant->tenantId();

        if (! $tenantId) {
            throw new RuntimeException('Tenant no resuelto.');
        }

        $tenant = $this->tenants->findById($tenantId);

        if (! $tenant) {
            throw new RuntimeException('Tenant no encontrado.');
        }

        return $tenant;
    }
}

