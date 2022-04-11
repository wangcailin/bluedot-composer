<?php

namespace Composer\Application\Push\Models;

use Illuminate\Database\Eloquent\Model;

class Push extends Model
{

    protected $table = 'push';

    protected $fillable = [
        'data',
        'data->tag',
        'data->msg',
        'push_time',
        'state',
        'type',
    ];

    protected $casts = [
        'data' => 'json',
    ];

    public function setPushTimeAttribute($value)
    {
        $this->attributes['push_time'] = date('Y-m-d H:i:00', strtotime($value));
    }
}
