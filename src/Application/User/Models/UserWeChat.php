<?php

namespace Composer\Application\User\Models;

use Illuminate\Database\Eloquent\Model;

class UserWeChat extends Model
{
    protected $table = 'user_wechat';

    protected $fillable = [
        'avatar',
        'sex',
        'remark',
        'nickname',
        'language',
        'country',
        'province',
        'city',
        'unionid',
    ];

    public function openid()
    {
        return $this->hasMany(UserWeChatOpenid::class, 'unionid', 'unionid');
    }

}
