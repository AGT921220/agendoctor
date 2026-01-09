<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Auth\Role;
use App\Domain\PatientPortal\Ports\PatientAuthRepository;
use App\Infrastructure\Persistence\Eloquent\Models\Patient;
use App\Infrastructure\Persistence\Eloquent\Models\PatientAuth;
use App\Infrastructure\Persistence\Eloquent\Models\User;
use DateTimeImmutable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

final class EloquentPatientAuthRepository implements PatientAuthRepository
{
    public function createOrRefreshInvitation(int $patientId, string $email, DateTimeImmutable $expiresAt, string $tokenPlain): void
    {
        $user = User::query()->where('email', $email)->first();

        if (! $user) {
            $user = User::query()->create([
                'name' => 'Paciente',
                'email' => $email,
                'password' => Hash::make(Str::random(32)),
                'role' => Role::PATIENT->value,
            ]);
        }

        $hash = hash('sha256', $tokenPlain);

        PatientAuth::query()->updateOrCreate(
            ['patient_id' => $patientId],
            [
                'user_id' => $user->id,
                'invitation_status' => 'pending',
                'invitation_token_hash' => $hash,
                'invitation_expires_at' => $expiresAt->format('Y-m-d H:i:s'),
                'invited_at' => (new DateTimeImmutable('now'))->format('Y-m-d H:i:s'),
                'accepted_at' => null,
            ]
        );
    }

    public function redeemInvitationToken(string $tokenPlain, DateTimeImmutable $now): ?array
    {
        $hash = hash('sha256', $tokenPlain);

        $row = PatientAuth::query()
            ->where('invitation_token_hash', $hash)
            ->first();

        if (! $row) {
            return null;
        }

        $expiresAt = $row->invitation_expires_at?->toDateTimeImmutable();
        if (! $expiresAt || $now > $expiresAt || $row->invitation_status !== 'pending') {
            // marcar expirado si corresponde
            if ($expiresAt && $now > $expiresAt && $row->invitation_status === 'pending') {
                $row->update(['invitation_status' => 'expired']);
            }
            return null;
        }

        // Canjear: no reutilizable
        $row->update([
            'invitation_status' => 'accepted',
            'invitation_token_hash' => null,
            'invitation_expires_at' => null,
            'accepted_at' => $now->format('Y-m-d H:i:s'),
        ]);

        if (! $row->user_id) {
            return null;
        }

        return [
            'user_id' => (int) $row->user_id,
            'patient_id' => (int) $row->patient_id,
        ];
    }

    public function findPatientIdByUserId(int $userId): ?int
    {
        $row = PatientAuth::query()->where('user_id', $userId)->first();

        if ($row) {
            return (int) $row->patient_id;
        }

        // Fallback: si por alguna razón no existe vínculo, intentamos vincular por email.
        $user = User::query()->find($userId);
        if (! $user || ! $user->email) {
            return null;
        }

        $patient = Patient::query()->where('email', (string) $user->email)->first();
        if (! $patient) {
            return null;
        }

        PatientAuth::query()->updateOrCreate(
            ['patient_id' => $patient->id],
            ['user_id' => $userId]
        );

        return (int) $patient->id;
    }
}

