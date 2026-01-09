<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

class PracticeScheduleBlock extends Model
{
    protected $table = 'practice_schedule_blocks';

    protected $fillable = [
        'day_of_week',
        'start_time',
        'end_time',
    ];
}

