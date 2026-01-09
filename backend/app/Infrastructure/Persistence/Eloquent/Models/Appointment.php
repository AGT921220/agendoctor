<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Database\Factories\AppointmentFactory;

class Appointment extends Model
{
    use HasFactory;

    protected $table = 'appointments';

    protected $fillable = [
        'patient_id',
        'starts_at',
        'duration_minutes',
        'status',
        'reason',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
    ];

    protected static function newFactory(): AppointmentFactory
    {
        return AppointmentFactory::new();
    }
}

