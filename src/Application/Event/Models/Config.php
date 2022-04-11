<?php

namespace Composer\Application\Event\Models;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    protected $table = 'event_config';

    protected $fillable = [
        'event_id',
        'value',
    ];

    protected $casts = [
        'value' => 'json',
    ];

}
