<?php

namespace Composer\Application\Tag\Models;

use Illuminate\Database\Eloquent\Model;

class TagType extends Model
{
    protected $table = 'tag_type';

    protected $fillable = [
        'name',
        'state',
    ];

    public function category()
    {
        return $this->hasMany(TagCategory::class, 'type_id', 'id');
    }

    public function children()
    {
        return $this->category()->select(['id', 'type_id', 'name as title']);
    }
}
