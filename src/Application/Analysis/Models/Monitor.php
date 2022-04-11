<?php

namespace Composer\Application\Analysis\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class Monitor extends Pivot
{

    protected $table = 'analysis_monitor';

    protected $fillable = [
        'type',
        'unionid',
        'openid',
        'user_id',
        'user_ip',
        'user_network',
        'user_agent',
        'page_title',
        'keywords',
        'page_description',
        'page_url',
        'page_referer_url',
        'page_param',
        'page_event_key',
        'page_event_type',
        'wechat_user_name',
        'wechat_appid',
        'wechat_event_key',
        'wechat_event_msg',
        'wechat_event_type',
        'wechat_event_data',
    ];

    protected $casts = [
        'wechat_event_data' => 'json',
        'page_param' => 'json',
        'keywords' => 'array',
    ];

}
