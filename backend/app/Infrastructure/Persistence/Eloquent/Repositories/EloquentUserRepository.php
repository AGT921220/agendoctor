<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Auth\Entities\User as UserEntity;
use App\Domain\Auth\Entities\UserAuthRecord;
use App\Domain\Auth\Ports\UserRepository;
use App\Domain\Auth\Role;
use App\Infrastructure\Persistence\Eloquent\Models\User;

final class EloquentUserRepository implements UserRepository
{
    public function findAuthByEmail(string $email): ?UserAuthRecord
    {
        $model = User::query()->where('email', $email)->first();

        if (! $model) {
            return null;
        }

        return new UserAuthRecord(
            id: (int) $model->id,
            name: (string) $model->name,
            email: (string) $model->email,
            role: Role::from((string) $model->role),
            passwordHash: (string) $model->password,
        );
    }

    public function findById(int $id): ?UserEntity
    {
        $model = User::query()->find($id);

        if (! $model) {
            return null;
        }

        return new UserEntity(
            id: (int) $model->id,
            name: (string) $model->name,
            email: (string) $model->email,
            role: Role::from((string) $model->role),
        );
    }
}

