<?php

namespace Composer\Application\Push\Models;

use Illuminate\Database\Eloquent\Model;

class PushJob extends Model
{
    protected $table = 'push_job';

    protected $fillable = [
        'data',
        'push_id',
        'state',
    ];

    protected $casts = [
        'data' => 'json',
    ];
}
