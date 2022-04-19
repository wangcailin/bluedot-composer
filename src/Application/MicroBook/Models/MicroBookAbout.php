<?php

namespace Composer\Application\MicroBook\Models;

use Illuminate\Database\Eloquent\Model;

class MicroBookAbout extends Model
{
    protected $table = 'microbook_about';

    protected $fillable = [
        'appid',
        'openid',
        'email',
    ];
}
