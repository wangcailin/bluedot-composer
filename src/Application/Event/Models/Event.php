<?php

namespace Composer\Application\Event\Models;

use Composer\Application\Event\Models\Register\Register;
use Composer\Application\Event\Models\Relation\EventTag;
use Composer\Application\Tag\Models\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $table = 'event';

    protected $fillable = [
        'event_type',
        'is_show',
        'title',
        'sub_title',
        'description',
        'banner',
        'qrcode',
        'start_time',
        'end_time',
        'module_list',

        'live_type',
        'live_room_id',
        'live_create_room_time',
        'live_stream_number',
        'live_push_address',
        'live_vod_id',
        'live_task_id',
        'live_playback',
        'live_inav',

        'address_country',
        'address_province',
        'address_city',
        'address_info',
        'address_detail_info',

    ];

    protected $casts = [
        'module_list' => 'array',
    ];

    protected $appends = ['time_state'];

    /**
     * 获取活动状态
     *
     * @param  string  $value
     * @return string
     */
    public function getTimeStateAttribute()
    {
        $timeState = 1;
        $time = time();
        $endTime = strtotime($this->end_time);
        $startTime = strtotime($this->start_time);
        if ($endTime < $time) {
            $timeState = 3;
        } else if ($startTime < $time && $time < $endTime) {
            $timeState = 2;
        }
        return $timeState;
    }

    public function scopeTimeState(Builder $query, $value): Builder
    {
        $time = date('Y-m-d H:i:s');

        if ($value == 1) {
            return $query->whereTime('start_time', '>', $time);
        } else if ($value == 2) {
            return $query->where('start_time', '>', $time)->where('end_time', '<', $time);
        } else if ($value == 3) {
            return $query->whereTime('end_time', '<', $time);
        }
    }

    public function register()
    {
        return $this->hasMany(Register::class, 'event_id', 'id');
    }

    public function tag()
    {
        return $this->belongsToMany(Tag::class, EventTag::class, 'event_id', 'tag_id')->withTimestamps();
    }
}
