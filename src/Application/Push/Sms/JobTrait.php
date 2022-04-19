<?php

namespace Composer\Application\Push\Sms;

use Composer\Support\Sms;
use Illuminate\Support\Facades\Log;

trait JobTrait
{
    /**
     * $query 发送参数
     */
    public function sendSmsHandle($query)
    {
        return Sms::send($query);
    }
}
