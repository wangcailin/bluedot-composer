<?php

namespace Composer\Application\Event\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $table = 'event_schedule';

    protected $fillable = [
        'event_id',
        'project_title',
        'topic_title',
        'sort',
        'speaker_ids',
        'start_time',
        'end_time',
        'description',
    ];

    protected $casts = [
        'speaker_ids' => 'array',
    ];

    protected $appends = ['speakers'];

    public function getSpeakersAttribute()
    {
        return Speaker::whereIn('id', $this->speaker_ids ?: [])->get();

    }

}
