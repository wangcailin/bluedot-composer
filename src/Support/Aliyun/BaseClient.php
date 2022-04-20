<?php

namespace Composer\Support\Aliyun;

use AlibabaCloud\Client\AlibabaCloud;

// Download：https://github.com/aliyun/openapi-sdk-php
// Usage：https://github.com/aliyun/openapi-sdk-php/blob/master/README.md

class BaseClient
{
    public static function client()
    {
        return AlibabaCloud::accessKeyClient(config('composer.aliyun_access_key_id'), config('composer.aliyun_access_key_secret'))
            ->regionId('cn-hangzhou')
            ->asDefaultClient();
    }
}
