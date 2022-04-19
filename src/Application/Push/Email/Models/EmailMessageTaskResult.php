<?php

namespace Composer\Application\Push\Email\Models;

use Illuminate\Database\Eloquent\Model;

class EmailMessageTaskResult extends Model
{
    protected $table = 'push_email_message_task_result';

    protected $fillable = ['email', 'result', 'params', 'state'];

    protected $casts = [
        'result' => 'json',
        'params' => 'json',
    ];
}
