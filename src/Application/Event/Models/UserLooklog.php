<?php

namespace Composer\Application\Event\Models;

use Illuminate\Database\Eloquent\Model;

class UserLooklog extends Model
{

    protected $table = 'event_userlooklog';

    protected $fillable = [
        'user_id',
        'event_id',
        'last_name',
        'start_time',
        'end_time',
        'tt',
        'pf',
        'browser',
        'viewer_province',
        'viewer_country',
        'duration',
        'type',
    ];
}
