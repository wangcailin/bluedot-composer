<?php

namespace Composer\Application\WeChat\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateMessage extends Model
{
    protected $table = 'wechat_template_message';

    protected $fillable = ['appid', 'template_id', 'title', 'primary_industry', 'deputy_industry', 'content', 'example', 'state'];
}
