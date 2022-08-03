<?php

namespace Composer\Support\Aliyun;

use Illuminate\Support\Env;
use OSS\OssClient as BaseOssClient;

class OssClient
{
    public static $OSS;

    public static function putObject($filename, $content, $option = [])
    {
        return self::OSSClient()->putObject(config('composer.aliyun_oss_bucket'), $filename, $content, $option);
    }

    public static function getObject($object, $option = [])
    {
        return self::OSSClient()->getObject(config('composer.aliyun_oss_bucket'), $object, $option);
    }

    public static function OSSClient()
    {
        return self::$OSS ?: self::$OSS = new BaseOssClient(config('composer.aliyun_access_key_id'), config('composer.aliyun_access_key_secret'), config('composer.aliyun_oss_endpoint'));
    }
}
