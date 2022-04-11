<?php

namespace Composer\Application\Event\Models;

use Composer\Application\Event\Models\Chat\Like;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $table = 'event_chat';

    protected $fillable = [
        'event_id',
        'user_id',
        'nick_name',
        'avatar_url',
        'state',
        'data',
        'like_count',
    ];

    public function getIsLikeAttribute()
    {
        $userId = request()->input('user_id');
        if (Like::where(['user_id' => $userId, 'event_chat_id' => $this->id])->exists()) {
            return 1;
        }
        return 0;
    }

}
