<?php

namespace Composer\Application\Config\System\Models;

use Illuminate\Database\Eloquent\Model;

class System extends Model
{
    protected $table = 'config_system';

    protected $fillable = [
        'data',
        'type',
    ];

    protected $casts = [
        'data' => 'json',
    ];
}
