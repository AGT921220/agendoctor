<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

class PracticeSettings extends Model
{
    protected $table = 'practice_settings';

    protected $fillable = [
        'timezone',
        'default_appointment_duration_minutes',
        'buffer_minutes',
        'confirm_cancel_cutoff_hours',
    ];
}

