<?php

namespace App\Application\PatientPortal\UseCases;

use App\Domain\Appointment\AppointmentStatus;
use App\Domain\Appointment\Ports\AppointmentRepository;
use App\Domain\PatientPortal\Ports\PatientAuthRepository;
use App\Domain\Practice\Ports\PracticeSettingsRepository;
use DateInterval;
use DateTimeImmutable;
use RuntimeException;

final readonly class PatientConfirmAppointmentUseCase
{
    public function __construct(
        private PatientAuthRepository $patientAuth,
        private AppointmentRepository $appointments,
        private PracticeSettingsRepository $settingsRepo,
    ) {
    }

    public function confirm(int $appointmentId, int $userId): void
    {
        $this->apply($appointmentId, $userId, 'confirm');
    }

    public function cancel(int $appointmentId, int $userId): void
    {
        $this->apply($appointmentId, $userId, 'cancel');
    }

    private function apply(int $appointmentId, int $userId, string $action): void
    {
        $patientId = $this->patientAuth->findPatientIdByUserId($userId);
        if (! $patientId) {
            throw new RuntimeException('Paciente no vinculado.');
        }

        $appt = $this->appointments->findById($appointmentId);
        if (! $appt || $appt->patientId !== $patientId) {
            throw new RuntimeException('Cita no encontrada.');
        }

        $settings = $this->settingsRepo->get();
        $cutoffHours = max(0, $settings->confirmCancelCutoffHours);

        $now = new DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $deadline = $appt->startsAt->sub(new DateInterval('PT'.$cutoffHours.'H'));

        if ($now > $deadline) {
            throw new RuntimeException('Fuera de la ventana permitida.');
        }

        if ($action === 'confirm') {
            if (! in_array($appt->status, [AppointmentStatus::scheduled, AppointmentStatus::confirmed], true)) {
                throw new RuntimeException('No se puede confirmar esta cita.');
            }
            $this->appointments->updateStatus($appointmentId, AppointmentStatus::confirmed->value);
            return;
        }

        if ($action === 'cancel') {
            if (! in_array($appt->status, [AppointmentStatus::scheduled, AppointmentStatus::confirmed], true)) {
                throw new RuntimeException('No se puede cancelar esta cita.');
            }
            $this->appointments->updateStatus($appointmentId, AppointmentStatus::cancelled->value);
            return;
        }

        throw new RuntimeException('Acción inválida.');
    }
}

