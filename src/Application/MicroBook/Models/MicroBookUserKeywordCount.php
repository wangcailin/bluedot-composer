<?php

namespace Composer\Application\MicroBook\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class MicroBookUserKeywordCount extends Pivot
{
    protected $table = 'microbook_user_keyword_count';

    protected $fillable = [
        'user_id',
        'unionid',
        'keyword',
        'count',
    ];
}
