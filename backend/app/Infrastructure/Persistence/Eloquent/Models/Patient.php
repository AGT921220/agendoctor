<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Database\Factories\PatientFactory;

class Patient extends Model
{
    use HasFactory;

    protected $table = 'patients';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'admin_notes',
    ];

    protected static function newFactory(): PatientFactory
    {
        return PatientFactory::new();
    }
}

