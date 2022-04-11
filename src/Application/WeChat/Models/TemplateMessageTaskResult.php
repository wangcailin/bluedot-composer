<?php

namespace Composer\Application\WeChat\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateMessageTaskResult extends Model
{
    protected $table = 'wechat_template_message_task_result';

    protected $fillable = ['openid', 'result', 'params', 'state'];

    protected $casts = [
        'result' => 'json',
        'params' => 'json',
    ];
}
