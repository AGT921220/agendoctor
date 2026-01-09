<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $table = 'subscriptions';

    protected $fillable = [
        'status',
        'plan_key',
        'stripe_customer_id',
        'stripe_subscription_id',
        'current_period_end',
        'limits_json',
    ];

    protected $casts = [
        'current_period_end' => 'datetime',
        'limits_json' => 'array',
    ];
}

