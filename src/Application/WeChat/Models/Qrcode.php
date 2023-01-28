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
        'reply_material_type',
        'reply_material_id',
        'auth_user_id'
    ];

    protected $casts = [
        'tag_ids' => 'array',
    ];
}
