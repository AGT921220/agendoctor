<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Patient\Entities\Patient as PatientEntity;
use App\Domain\Patient\Ports\PatientRepository;
use App\Infrastructure\Persistence\Eloquent\Models\Patient;

final class EloquentPatientRepository implements PatientRepository
{
    public function findById(int $id): ?PatientEntity
    {
        $p = Patient::query()->find($id);

        if (! $p) {
            return null;
        }

        return new PatientEntity(
            id: (int) $p->id,
            firstName: (string) $p->first_name,
            lastName: $p->last_name ? (string) $p->last_name : null,
            email: $p->email ? (string) $p->email : null,
            phone: $p->phone ? (string) $p->phone : null,
            adminNotes: $p->admin_notes ? (string) $p->admin_notes : null,
        );
    }
}

