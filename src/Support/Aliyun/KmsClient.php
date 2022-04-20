<?php

namespace Composer\Support\Aliyun;

use AlibabaCloud\Kms\Kms;

use AlibabaCloud\Client\AlibabaCloud;

class KmsClient
{
    public static $client;

    private static function createClient()
    {
        if (!self::$client) {
            AlibabaCloud::accessKeyClient(config('composer.aliyun_access_key_id'), config('composer.aliyun_access_key_secret'))
                ->regionId('cn-beijing')
                ->asDefaultClient();
            self::$client = true;
        }
    }

    /**
     *  创建普通凭据
     */
    public static function createSecret($secretName, $secretData)
    {
        self::createClient();
        $request = Kms::v20160120()->createSecret();
        return $request
            ->withSecretName($secretName)
            ->withVersionId('1')
            ->withSecretData(json_encode($secretData))
            ->request()->toArray();
    }

    /**
     *  获取凭据值
     */
    public static function getSecret($secretName)
    {
        self::createClient();
        $request = Kms::v20160120()->getSecretValue();
        $result = $request
            ->withSecretName($secretName)
            ->request()->toArray();
        return json_decode($result['SecretData'], true);
    }

    /**
     *  获取凭据值
     */
    public static function getCentralSecret()
    {
        return [
            'iv' => config('composer.aes_iv'),
            'key' => config('composer.aes_key'),
        ];
    }

    /**
     *  删除凭据对象
     */
    public static function deleteSecret($secretName)
    {
        self::createClient();
        $request = Kms::v20160120()->deleteSecret();
        $result = $request
            ->withSecretName($secretName)
            ->request();
        return $result->toArray();
    }
}
