<?php

namespace Composer\Support;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
use Illuminate\Support\Facades\Log;

// Download：https://github.com/aliyun/openapi-sdk-php
// Usage：https://github.com/aliyun/openapi-sdk-php/blob/master/README.md
class Sms
{
    public static function createClient()
    {
        AlibabaCloud::accessKeyClient(env('SMS_ACCESS_KEY_ID', ''), env('SMS_ACCESS_KEY_SECRET', ''))
            ->regionId('ap-northeast-1')
            ->asDefaultClient();
    }

    public static function send($query)
    {
        self::createClient();
        try {
            $result = AlibabaCloud::rpc()
                ->product('Dysmsapi')
                ->version('2017-05-25')
                ->action('SendSms')
                ->method('POST')
                ->host('dysmsapi.aliyuncs.com')
                ->options([
                    'query' => $query,
                ])
                ->request();
            Log::info($result);
            return $result->toArray();
        } catch (ClientException $e) {
            Log::error($e->getErrorMessage());
            return false;
        } catch (ServerException $e) {
            Log::error($e->getErrorMessage());
            return false;
        }
    }
}
