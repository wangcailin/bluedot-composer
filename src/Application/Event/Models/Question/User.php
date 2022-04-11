<?php

namespace Composer\Application\Event\Models\Question;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'event_question_user';

    protected $fillable = [
        'event_id',
        'user_id',
        'unionid',
        'qa',
    ];

    protected $casts = [
        'qa' => 'json',
    ];

}
