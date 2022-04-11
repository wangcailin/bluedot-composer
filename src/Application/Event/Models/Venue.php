<?php

namespace Composer\Application\Event\Models;

use Illuminate\Database\Eloquent\Model;

class Venue extends Model
{
    protected $table = 'event_venue';

    protected $fillable = [
        'event_id',
        'img_list',
    ];

    protected $casts = [
        'img_list' => 'array',
    ];
}
