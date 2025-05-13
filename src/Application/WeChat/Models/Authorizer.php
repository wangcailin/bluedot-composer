<?php

namespace BluedotComposer\Application\WeChat\Models;

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
}
