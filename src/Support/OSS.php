<?php

namespace Composer\Support;

use Illuminate\Support\Env;
use OSS\OssClient;

class OSS
{
    public static $OSS;

    public static function putObject($filename, $content, $option = [])
    {
        return self::OSSClient()->putObject(Env::get('OSS_BUCKET', ''), $filename, $content, $option);
    }

    public static function OSSClient()
    {
        return self::$OSS ?: self::$OSS = new OssClient(Env::get('OSS_ACCESS_KEY_ID', ''), Env::get('OSS_ACCESS_KEY_SECRET', ''), Env::get('OSS_ENDPOINT', ''));
    }
}
