<?php

namespace App\Application\PatientPortal\UseCases;

use App\Domain\Appointment\Ports\AppointmentRepository;
use App\Domain\PatientPortal\Ports\PatientAuthRepository;
use DateTimeImmutable;
use RuntimeException;

final readonly class PatientListMyAppointmentsUseCase
{
    public function __construct(
        private PatientAuthRepository $patientAuth,
        private AppointmentRepository $appointments,
    ) {
    }

    /**
     * @return array{upcoming: list<array<string,mixed>>, history: list<array<string,mixed>>}
     */
    public function execute(int $userId): array
    {
        $patientId = $this->patientAuth->findPatientIdByUserId($userId);
        if (! $patientId) {
            throw new RuntimeException('Paciente no vinculado.');
        }

        $now = new DateTimeImmutable('now', new \DateTimeZone('UTC'));

        $fromHistory = $now->modify('-3 years');
        $toUpcoming = $now->modify('+1 year');

        $all = $this->appointments->listByPatientBetween($patientId, $fromHistory, $toUpcoming);

        $upcoming = [];
        $history = [];

        foreach ($all as $appt) {
            $item = [
                'id' => $appt->id,
                'starts_at' => $appt->startsAt->format(DATE_ATOM),
                'duration_minutes' => $appt->durationMinutes,
                'status' => $appt->status->value,
                'reason' => $appt->reason,
            ];

            if ($appt->startsAt >= $now) {
                $upcoming[] = $item;
            } else {
                $history[] = $item;
            }
        }

        return [
            'upcoming' => $upcoming,
            'history' => $history,
        ];
    }
}

