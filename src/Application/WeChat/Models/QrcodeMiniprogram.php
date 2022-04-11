<?php

namespace Composer\Application\WeChat\Models;

use Illuminate\Database\Eloquent\Model;

class QrcodeMiniprogram extends Model
{
    protected $table = 'wechat_qrcode_miniprogram';

    protected $fillable = [
        'appid',
        'scene_str',
        'value',
    ];

    protected $casts = [
        'value' => 'json',
    ];

}
