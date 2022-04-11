<?php

namespace Composer\Application\WeChat\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $table = 'wechat_material';

    protected $fillable = [
        'name',
        'type',
        'data',
        'remark',
        'appid',
        'created_at',
        'updated_at',
        'auth_user_id'
    ];

    protected $casts = [
        'data' => 'json',
    ];
}
