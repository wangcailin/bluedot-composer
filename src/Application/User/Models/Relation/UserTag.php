<?php

namespace Composer\Application\User\Models\Relation;

use Composer\Application\Tag\Models\Tag;
use Illuminate\Database\Eloquent\Relations\Pivot;

class UserTag extends Pivot
{
    protected $table = 'user_tag_relation';

    protected static function booted()
    {
        static::created(function ($row) {
            $data = ['user_id' => $row->user_id, 'tag_id' => $row->tag_id];
            UserTagCount::firstOrCreate($data);
            UserTagCount::where($data)->increment('count');
        });
    }

    public function tag()
    {
        return $this->hasMany(Tag::class, 'user_id', 'tag_id');
    }
}
