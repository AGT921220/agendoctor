<?php

namespace App\Domain\Appointment;

enum AppointmentStatus: string
{
    case scheduled = 'scheduled';
    case confirmed = 'confirmed';
    case attended = 'attended';
    case no_show = 'no_show';
    case cancelled = 'cancelled';
}

