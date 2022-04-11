<?php

namespace Composer\Application\Event\Models\Register;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    protected $table = 'event_register_config';

    protected $fillable = [
        'event_id',
        'over_time',
        'field_list',
    ];

    protected $casts = [
        'field_list' => 'array',
    ];

    protected $appends = ['time_state'];

    public function getTimeStateAttribute()
    {
        $timeState = 1;
        $time = date('Y-m-d H:i:s');
        if ($this->over_time > $time) {
            $timeState = 0;
        }
        return $timeState;
    }

}
