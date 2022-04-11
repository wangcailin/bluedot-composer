<?php

namespace Composer\Application\Auth\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;


class OperationLog extends Pivot
{
    protected $table = 'auth_operation_log';

    protected $fillable = ['user_id', 'path', 'method', 'ip', 'input'];
}
