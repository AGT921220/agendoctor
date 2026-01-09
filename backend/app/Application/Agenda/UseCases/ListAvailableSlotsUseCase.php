<?php

namespace App\Application\Agenda\UseCases;

use App\Application\Agenda\DTOs\AvailableSlotsResult;
use App\Domain\Appointment\Entities\TimeRange;
use App\Domain\Appointment\Ports\AppointmentRepository;
use App\Domain\Practice\Ports\PracticeSettingsRepository;
use DateInterval;
use DateTimeImmutable;
use DateTimeZone;

final readonly class ListAvailableSlotsUseCase
{
    public function __construct(
        private PracticeSettingsRepository $settingsRepo,
        private AppointmentRepository $appointments,
    ) {
    }

    public function execute(string $dateYmd, ?int $durationMinutes = null): AvailableSlotsResult
    {
        $settings = $this->settingsRepo->get();

        $tz = new DateTimeZone($settings->timezone);
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $dateYmd, $tz);

        if (! $date) {
            throw new \InvalidArgumentException('Fecha inválida.');
        }

        if (in_array($dateYmd, $settings->holidays, true)) {
            return new AvailableSlotsResult(
                date: $dateYmd,
                timezone: $settings->timezone,
                durationMinutes: $durationMinutes ?? $settings->defaultAppointmentDurationMinutes,
                slots: [],
            );
        }

        $duration = $durationMinutes ?? $settings->defaultAppointmentDurationMinutes;
        if ($duration <= 0) {
            throw new \InvalidArgumentException('Duración inválida.');
        }

        // Día de semana: 1 (Mon) ... 7 (Sun)
        $dayOfWeek = (int) $date->format('N');
        $blocks = array_values(array_filter(
            $settings->scheduleBlocks,
            fn ($b) => $b->dayOfWeek === $dayOfWeek
        ));

        if ($blocks === []) {
            return new AvailableSlotsResult($dateYmd, $settings->timezone, $duration, []);
        }

        // Cargamos citas del día (en UTC) para construir intervalos ocupados con buffer.
        $dayStartLocal = $date;
        $dayEndLocal = $date->modify('+1 day');
        $dayStartUtc = $dayStartLocal->setTimezone(new DateTimeZone('UTC'));
        $dayEndUtc = $dayEndLocal->setTimezone(new DateTimeZone('UTC'));

        $existing = $this->appointments->listBetween($dayStartUtc, $dayEndUtc);

        $buffer = max(0, $settings->bufferMinutes);
        $busy = [];
        foreach ($existing as $appt) {
            $start = $appt->startsAt;
            $end = $appt->startsAt->add(new DateInterval('PT'.$appt->durationMinutes.'M'));
            // Intervalo protegido: [start-buffer, end+buffer) (half-open)
            $busy[] = new TimeRange(
                start: $start->sub(new DateInterval('PT'.$buffer.'M')),
                end: $end->add(new DateInterval('PT'.$buffer.'M')),
            );
        }

        // Ordenar busy por inicio para chequeo más simple
        usort($busy, fn (TimeRange $a, TimeRange $b) => $a->start <=> $b->start);

        $slots = [];
        $stepMinutes = 5;

        foreach ($blocks as $block) {
            [$sh, $sm] = array_map('intval', explode(':', $block->startTime));
            [$eh, $em] = array_map('intval', explode(':', $block->endTime));

            $blockStartLocal = $date->setTime($sh, $sm);
            $blockEndLocal = $date->setTime($eh, $em);

            // último inicio permitido para que quepa duración
            $lastStartLocal = $blockEndLocal->sub(new DateInterval('PT'.$duration.'M'));
            $cursorLocal = $blockStartLocal;

            while ($cursorLocal <= $lastStartLocal) {
                $candidateStartUtc = $cursorLocal->setTimezone(new DateTimeZone('UTC'));
                $candidateEndUtc = $candidateStartUtc->add(new DateInterval('PT'.$duration.'M'));

                if (! $this->overlapsAny($candidateStartUtc, $candidateEndUtc, $busy)) {
                    $slots[] = $cursorLocal->format(DATE_ATOM);
                }

                $cursorLocal = $cursorLocal->add(new DateInterval('PT'.$stepMinutes.'M'));
            }
        }

        return new AvailableSlotsResult(
            date: $dateYmd,
            timezone: $settings->timezone,
            durationMinutes: $duration,
            slots: $slots,
        );
    }

    /**
     * Intervalos half-open: [start, end)
     *
     * @param  list<TimeRange>  $busy
     */
    private function overlapsAny(DateTimeImmutable $start, DateTimeImmutable $end, array $busy): bool
    {
        foreach ($busy as $range) {
            // overlap if start < range.end AND end > range.start
            if ($start < $range->end && $end > $range->start) {
                return true;
            }
        }

        return false;
    }
}

