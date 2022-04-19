<?php

namespace Composer\Application\Tag\Models;

use Illuminate\Database\Eloquent\Model;

class TagCategory extends Model
{
    protected $table = 'tag_category';

    protected $fillable = [
        'name',
        'state',
    ];

    public function tag()
    {
        return $this->hasMany(Tag::class, 'category_id', 'id');
    }

    public function children()
    {
        return $this->tag()->select(['id', 'category_id', 'id as value', 'name as title']);
    }
}
