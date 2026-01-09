<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Eloquent sin lógica de negocio.
 */
class Tenant extends Model
{
    protected $table = 'tenants';

    protected $fillable = [
        'name',
    ];
}

