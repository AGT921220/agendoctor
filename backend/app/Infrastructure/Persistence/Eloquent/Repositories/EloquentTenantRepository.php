<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Tenant\Entities\Tenant as TenantEntity;
use App\Domain\Tenant\Ports\TenantRepository;
use App\Infrastructure\Persistence\Eloquent\Models\Tenant;

final class EloquentTenantRepository implements TenantRepository
{
    public function findById(int $id): ?TenantEntity
    {
        $model = Tenant::query()->find($id);

        if (! $model) {
            return null;
        }

        return new TenantEntity(
            id: (int) $model->id,
            name: (string) $model->name,
        );
    }
}

