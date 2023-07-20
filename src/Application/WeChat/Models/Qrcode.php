<?php

namespace Composer\Application\WeChat\Models;

use Illuminate\Database\Eloquent\Model;

class Qrcode extends Model
{
    protected $table = 'wechat_qrcode';

    protected $fillable = [
        'name',
        'tag_ids',
        'scene_str',
        'ticket',
        'url',
        'remark',
        'appid',
        'auth_user_id',
        'replys'
    ];

    protected $casts = [
        'tag_ids' => 'array',
        'replys' => 'array',
    ];
}
