<?php

namespace Composer\Application\WeChat\Models;

use Composer\Application\Analysis\Models\Monitor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Qrcode extends Model
{
    protected $table = 'wechat_qrcode';

    protected $fillable = [
        'name',
        'tag_ids',
        'scene_str',
        'ticket',
        'url',
        'remark',
        'appid',
        'reply_material_type',
        'reply_material_id',
        'auth_user_id'
    ];

    protected $casts = [
        'tag_ids' => 'array',
    ];

    protected $appends = ['timeline'];

    public function monitor()
    {
        return $this->hasMany(Monitor::class, 'wechat_event_key', 'scene_str');
    }

    public function authorizer()
    {
        return $this->hasOne(Authorizer::class, 'appid', 'appid');
    }

    public function getTimelineAttribute()
    {

        $start_date = (time() - 86400 * 5);
        $end_date = (time() + 86400);
        $cut = ($end_date - $start_date) / 86400;
        $data = [];
        for ($i = 0; $i <= $cut; $i++) {
            $data[date('Y-m-d', $start_date * $i)] = 0;
        }
        $date = [date('Y-m-d', $start_date), date('Y-m-d', $end_date)];

        Monitor::select([DB::raw("to_char ( created_at, 'YYYY-MM-DD') AS datetime"), DB::raw("count(1) as count")])
            ->where('wechat_event_key', $this->scene_str)
            ->whereBetween('created_at', $date)
            ->groupBy('datetime')
            ->get()
            ->map(function ($v) use (&$data) {
                $data[$v->datetime] = $v->count;
            });

        $timeline = [];
        foreach ($data as $value) {
            $timeline[] = $value;
        }
        return $timeline;
    }
}
