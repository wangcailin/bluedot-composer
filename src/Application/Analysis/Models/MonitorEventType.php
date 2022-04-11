<?php

namespace App\Models\Analysis;

use Illuminate\Database\Eloquent\Model;

class MonitorEventType extends Model
{
    protected $table = 'analysis_monitor_event_type';

    protected $fillable = [
        'name',
        'state',
    ];
}
