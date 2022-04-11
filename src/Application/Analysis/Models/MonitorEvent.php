<?php

namespace App\Models\Analysis;

use Illuminate\Database\Eloquent\Model;

class MonitorEvent extends Model
{

    protected $table = 'analysis_monitor_event';

    protected $fillable = [
        'type',
        'name',
        'state',
        'remark',
        'tags',
        'parent_id',
        'type',
    ];

    protected $casts = [
        'tags' => 'array',
    ];

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }
}
