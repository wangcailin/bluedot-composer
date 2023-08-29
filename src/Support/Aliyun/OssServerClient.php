<?php

namespace Composer\Support\Aliyun;

use OSS\OssClient as BaseOssClient;

class OssServerClient
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

    public static function deleteObject($object)
    {
        return self::OSSClient()->deleteObject(config('composer.aliyun_oss_bucket'), $object);
    }

    public static function OSSClient()
    {
        if (self::$OSS) return self::$OSS;

        self::$OSS = new BaseOssClient(config('composer.aliyun_access_key_id'), config('composer.aliyun_access_key_secret'), config('composer.aliyun_oss_endpoint'));
        self::$OSS->setUseSSL(true);
        return self::$OSS;
    }
}
