<?php

namespace Composer\Application\WeChat\Models;

use Illuminate\Database\Eloquent\Model;

class Authorizer extends Model
{
    protected $table = 'wechat_authorizer';

    protected $fillable = [
        'open_appid',
        'appid',
        'nick_name',
        'head_img',
        'user_name',
        'type',
        'app_type',
        'subscribe',
        'auth_user_id'
    ];

    protected $appends = ['type_text'];

    public function getTypeTextAttribute()
    {
        $data = [
            1 => '公众号',
            2 => '小程序',
        ];

        if ($this->type && !empty($data[$this->type])) {
            return $data[$this->type];
        }
    }
}
