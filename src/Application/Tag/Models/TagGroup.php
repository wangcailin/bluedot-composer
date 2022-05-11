<?php

namespace Composer\Application\Tag\Models;

use Illuminate\Database\Eloquent\Model;

class TagGroup extends Model
{
    protected $table = 'tag_group';

    protected $fillable = [
        'name',
        'state',
    ];

    public function tag()
    {
        return $this->hasMany(Tag::class, 'group_id', 'id');
    }

    public function children()
    {
        return $this->tag()->select(['id', 'group_id', 'id as value', 'name as title']);
    }
}
