<?php

namespace Composer\Application\Event\Models\Question;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $table = 'event_question';

    protected $fillable = [
        'event_id',
        'start_time',
        'end_time',
        'qa',
    ];

    protected $casts = [
        'qa' => 'array',
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
