<?php

namespace Composer\Application\MicroBook\Models;

use Illuminate\Database\Eloquent\Model;

class MicroBookConfig extends Model
{
    protected $table = 'microbook_config';

    protected $fillable = [
        'appid',
        'value',
    ];

    protected $casts = [
        'value' => 'json',
    ];
}
