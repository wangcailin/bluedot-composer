<?php

namespace Composer\Application\User\Models;

use Illuminate\Database\Eloquent\Model;

class UserInfo extends Model
{
    protected $table = 'user_info';

    protected $fillable = [
        'full_name',
        'first_name',
        'last_name',
        'company',
        'user_id',
        'extend',
    ];

    protected $casts = [
        'extend' => 'json',
    ];
}
