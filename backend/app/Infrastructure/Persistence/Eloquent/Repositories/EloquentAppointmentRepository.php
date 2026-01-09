<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Appointment\AppointmentStatus;
use App\Domain\Appointment\Entities\Appointment as AppointmentEntity;
use App\Domain\Appointment\Ports\AppointmentRepository;
use App\Infrastructure\Persistence\Eloquent\Models\Appointment;
use DateTimeImmutable;
use DateTimeZone;

final class EloquentAppointmentRepository implements AppointmentRepository
{
    public function listBetween(DateTimeImmutable $fromInclusive, DateTimeImmutable $toExclusive): array
    {
        $rows = Appointment::query()
            ->where('starts_at', '>=', $fromInclusive->format('Y-m-d H:i:s'))
            ->where('starts_at', '<', $toExclusive->format('Y-m-d H:i:s'))
            ->orderBy('starts_at')
            ->get();

        return $rows->map(fn (Appointment $a) => $this->toEntity($a))->all();
    }

    public function listByPatientBetween(int $patientId, DateTimeImmutable $fromInclusive, DateTimeImmutable $toExclusive): array
    {
        $rows = Appointment::query()
            ->where('patient_id', $patientId)
            ->where('starts_at', '>=', $fromInclusive->format('Y-m-d H:i:s'))
            ->where('starts_at', '<', $toExclusive->format('Y-m-d H:i:s'))
            ->orderBy('starts_at')
            ->get();

        return $rows->map(fn (Appointment $a) => $this->toEntity($a))->all();
    }

    public function findById(int $id): ?AppointmentEntity
    {
        $row = Appointment::query()->find($id);

        return $row ? $this->toEntity($row) : null;
    }

    public function updateStatus(int $id, string $status): void
    {
        Appointment::query()->whereKey($id)->update([
            'status' => $status,
        ]);
    }

    private function toEntity(Appointment $a): AppointmentEntity
    {
        $startsAtUtc = DateTimeImmutable::createFromFormat(
            'Y-m-d H:i:s',
            $a->starts_at->format('Y-m-d H:i:s'),
            new DateTimeZone('UTC'),
        );

        return new AppointmentEntity(
            id: (int) $a->id,
            patientId: (int) $a->patient_id,
            startsAt: $startsAtUtc ?: new DateTimeImmutable('now', new DateTimeZone('UTC')),
            durationMinutes: (int) $a->duration_minutes,
            status: AppointmentStatus::from((string) $a->status),
            reason: $a->reason ? (string) $a->reason : null,
        );
    }
}

