<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Auth\Entities\User as UserEntity;
use App\Domain\Auth\Entities\UserAuthRecord;
use App\Domain\Auth\Ports\UserRepository;
use App\Domain\Auth\Role;
use App\Domain\Tenant\Ports\CurrentTenantProvider;
use App\Infrastructure\Persistence\Eloquent\Models\User;

final class EloquentUserRepository implements UserRepository
{
    public function __construct(
        private readonly CurrentTenantProvider $currentTenant,
    ) {
    }

    public function findAuthByEmail(string $email): ?UserAuthRecord
    {
        $query = User::query()->where('email', $email);

        // Si el tenant ya está resuelto (request autenticada), restringimos por tenant_id.
        $tenantId = $this->currentTenant->tenantId();
        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        $model = $query->first();

        if (! $model) {
            return null;
        }

        return new UserAuthRecord(
            id: (int) $model->id,
            tenantId: (int) $model->tenant_id,
            name: (string) $model->name,
            email: (string) $model->email,
            role: Role::from((string) $model->role),
            passwordHash: (string) $model->password,
        );
    }

    public function findById(int $id): ?UserEntity
    {
        $query = User::query()->whereKey($id);

        $tenantId = $this->currentTenant->tenantId();
        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        $model = $query->first();

        if (! $model) {
            return null;
        }

        return new UserEntity(
            id: (int) $model->id,
            tenantId: (int) $model->tenant_id,
            name: (string) $model->name,
            email: (string) $model->email,
            role: Role::from((string) $model->role),
        );
    }
}

