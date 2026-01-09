<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

class PatientAuth extends Model
{
    protected $table = 'patient_auths';

    protected $fillable = [
        'patient_id',
        'user_id',
        'invitation_status',
        'invitation_token_hash',
        'invitation_expires_at',
        'invited_at',
        'accepted_at',
    ];

    protected $casts = [
        'invitation_expires_at' => 'datetime',
        'invited_at' => 'datetime',
        'accepted_at' => 'datetime',
    ];
}

