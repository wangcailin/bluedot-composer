<?php

namespace Composer\Application\Event\Models\Live;

use Illuminate\Database\Eloquent\Model;

class PPT extends Model
{
    protected $table = 'event_live_ppt';

    protected $fillable = [
        'event_id',
        'filelist',
        'active_index',
    ];

    protected $casts = [
        'filelist' => 'array',
    ];

}
