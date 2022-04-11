<?php

namespace Composer\Application\User\Models\Relation;

use Composer\Application\Tag\Models\Tag;
use Illuminate\Database\Eloquent\Relations\Pivot;

class UserTagCount extends Pivot
{
    protected $table = 'user_tag_count_relation';

    public function tag()
    {
        return $this->hasOne(Tag::class, 'id', 'tag_id');
    }

}
