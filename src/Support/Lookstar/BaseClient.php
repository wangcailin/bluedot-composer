<?php

namespace Composer\Support\Lookstar;

use Lookstar\ApiClient;

class BaseClient
{
    public static $client;

    public static function getClient()
    {
        if (!self::$client) {
            $clientId = config('composer.lookstar_client_id'); // 第三方用户唯一凭证
            $clientSecret = config('composer.lookstar_client_secret'); // 第三方用户唯一凭证密钥，即 appsecret
            $tenantId = config('composer.lookstar_tenant_id'); // 租户 ID

            self::$client = new ApiClient($clientId, $clientSecret, $tenantId);
        }
    }

    public static function client()
    {
        self::getClient();
        return self::$client;
    }
}
