<?php

namespace Composer\Application\Event\Models\Vote;

use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    protected $table = 'event_vote';

    protected $fillable = [
        'event_id',
        'qa',
        'schedule_id',
        'state',
    ];

    protected $casts = [
        'qa' => 'json',
    ];

}
