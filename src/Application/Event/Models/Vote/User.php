<?php

namespace Composer\Application\Event\Models\Vote;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'event_vote_user';

    protected $fillable = [
        'schedule_id',
    ];
}
