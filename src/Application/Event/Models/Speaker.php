<?php

namespace Composer\Application\Event\Models;

use Illuminate\Database\Eloquent\Model;

class Speaker extends Model
{
    protected $table = 'event_speaker';

    protected $fillable = [
        'event_id',
        'name',
        'job',
        'description',
        'img',
        'is_sign_page',
        'sort',
    ];

}
