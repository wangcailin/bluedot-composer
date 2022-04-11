<?php

namespace Composer\Application\Event\Models;

use Illuminate\Database\Eloquent\Model;

class DataDownload extends Model
{
    protected $table = 'event_data_download';

    protected $fillable = [
        'event_id',
        'start_time',
        'end_time',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    protected $appends = ['time_state'];

    public function getTimeStateAttribute()
    {
        $timeState = 0;
        $time = time();
        if (strtotime($this->start_time) < $time && strtotime($this->end_time) > $time) {
            $timeState = 1;
        }
        return $timeState;
    }
}
