<?php

namespace Composer\Application\Push\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class VerifyCode extends Pivot
{
    protected $table = 'verify_code';

    protected $fillable = [
        'expires_in',
        'code',
        'state',
        'type',
        'value',
        'action',
    ];
}
