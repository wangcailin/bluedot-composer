<?php

namespace Composer\Application\Config\Lifecycle\Models;

use Illuminate\Database\Eloquent\Model;

class Lifecycle extends Model
{
    protected $table = 'config_lifecycle';

    protected $fillable = [
        'data',
    ];

    protected $casts = [
        'data' => 'json',
    ];
}
