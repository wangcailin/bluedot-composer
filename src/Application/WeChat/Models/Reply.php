<?php

namespace Composer\Application\WeChat\Models;

use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    protected $table = 'wechat_reply';

    protected $fillable = [
        'appid',
        'match',
        'text',
        'remark',
        'appid',
        'tag_ids',
        'type',
        'auth_user_id',
        'replys'
    ];

    protected $casts = [
        'tag_ids' => 'array',
        'replys' => 'array',
    ];

    public function authorizer()
    {
        return $this->hasOne(Authorizer::class, 'appid', 'appid');
    }
}
