<?php

namespace Composer\Application\MicroBook\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class MicroBookUserKeyword extends Pivot
{
    protected $table = 'microbook_user_keyword';

    protected $fillable = [
        'user_id',
        'unionid',
        'keyword',
        'article_id',
    ];

    protected static function booted()
    {
        static::created(function ($row) {
            $data = ['user_id' => $row->user_id, 'unionid' => $row->unionid, 'keyword' => $row->keyword];
            MicroBookUserKeywordCount::firstOrCreate($data);
            MicroBookUserKeywordCount::where($data)->increment('count');
        });
    }
}
