<?php

namespace Composer\Application\Event\Models\Chat;

use Illuminate\Database\Eloquent\Relations\Pivot;

class Like extends Pivot
{
    protected $table = 'event_chat_like';

    protected $fillable = [
        'event_chat_id',
        'user_id',
        'unionid',
    ];
}
