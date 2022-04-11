<?php

namespace Composer\Application\MicroBook\Models;

use Illuminate\Database\Eloquent\Model;

class MicroBookCategory extends Model
{
    protected $table = 'microbook_category';

    protected $fillable = [
        'name',
        'state',
        'sort',
    ];

    public function microbook()
    {
        return $this->hasMany(MicroBook::class, 'category_id', 'id');
    }
}
