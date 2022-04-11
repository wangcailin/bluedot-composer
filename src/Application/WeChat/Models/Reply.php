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
        'reply_material_type',
        'reply_material_id',
        'remark',
        'appid',
        'tag_ids',
        'type',
        'auth_user_id'
    ];

    protected $casts = [
        'tag_ids' => 'array',
    ];

    public function authorizer()
    {
        return $this->hasOne(Authorizer::class, 'appid', 'appid');
    }
}
