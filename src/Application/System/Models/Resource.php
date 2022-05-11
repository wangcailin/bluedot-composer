<?php

namespace Composer\Application\System\Models;

use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    protected $table = 'system_resource';

    protected $fillable = [
        'title',
        'mime_type',
        'extension',
        'size',
        'url',
        'app_source'
    ];
}
