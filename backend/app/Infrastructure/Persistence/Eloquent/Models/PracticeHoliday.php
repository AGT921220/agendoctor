<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

class PracticeHoliday extends Model
{
    protected $table = 'practice_holidays';

    protected $fillable = [
        'date',
        'label',
    ];
}

