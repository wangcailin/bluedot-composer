<?php

namespace Composer\Application\Tag\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $table = 'tag';

    protected $fillable = [
        'group_id',
        'name',
        'remark',
        'state',
    ];
}
