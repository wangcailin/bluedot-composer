<?php

namespace Composer\Application\WeChat\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateMessageTask extends Model
{
    protected $table = 'wechat_template_message_task';

    protected $fillable = ['template_id', 'title', 'content', 'send_state', 'send_time', 'send_group', 'url'];

    protected $casts = [
        'send_group' => 'array',
        'content' => 'json',
    ];

    public function setSendTimeAttribute($value)
    {
        $this->attributes['send_time'] = date('Y-m-d H:i:00', strtotime($value));
    }

}
