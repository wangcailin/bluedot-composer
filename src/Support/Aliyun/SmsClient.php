<?php

namespace Composer\Support\Aliyun;

use AlibabaCloud\SDK\Dysmsapi\V20170525\Dysmsapi;
use Darabonba\OpenApi\Models\Config;
use AlibabaCloud\SDK\Dysmsapi\V20170525\Models\SendSmsRequest;

// composer require alibabacloud/dysmsapi-20170525 2.0.23
class SmsClient
{
    public static $client;

    private static function createClient()
    {
        if (!self::$client) {
            $config = new Config([
                'accessKeyId' => config('composer.aliyun_access_key_id'),
                'accessKeySecret' => config('composer.aliyun_access_key_secret')
            ]);
            $config->endpoint = 'dysmsapi.aliyuncs.com';
            self::$client = new Dysmsapi($config);
        }
    }

    /**
     * 发送登录验证码
     */
    public static function sendBackendLoginCode($phone, $code)
    {
        self::createClient();
        $sendSmsRequest = new SendSmsRequest([
            'phoneNumbers' => $phone,
            'signName' => config('composer.aliyun_sms_sign_name'),
            'templateCode' => config('composer.aliyun_sms_template_code'),
            'templateParam' => "{\"code\": \"{$code}\"}"
        ]);
        return self::$client->sendSms($sendSmsRequest);
    }
}
