<?php

namespace Composer\Application\Tag\Models;

use Composer\Application\User\Models\Relation\UserTag;
use Composer\Application\User\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Tag extends Model
{
    protected $table = 'tag';

    protected $fillable = [
        'category_id',
        'name',
        'remark',
        'state',
    ];

    // protected $appends = ['timeline'];

    public function user()
    {
        return $this->belongsToMany(User::class, UserTag::class, 'tag_id', 'user_id')->withTimestamps();
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

        UserTag::select([DB::raw("to_char ( created_at, 'YYYY-MM-DD') AS datetime"), DB::raw("count(1) as count")])
            ->where('tag_id', $this->id)
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
