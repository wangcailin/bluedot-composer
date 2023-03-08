<?php

namespace Composer\Application\WeChat;

use EasyWeChat\OfficialAccount\Application as OfficialAccountApplication;
use EasyWeChat\MiniApp\Application as MiniAppApplication;
use EasyWeChat\OpenPlatform\Application as OpenPlatformApplication;

class WeChat
{
    /** 开放平台 */
    public $openPlatform = null;

    /** 公众号 */
    public $officialAccount = null;

    /** 小程序 */
    public $miniProgram = null;

    /**
     * 授权Model
     */
    public $authorizerModel = null;

    /**
     * 获取公众号实例
     */
    public function getOfficialAccount(string $appId): OfficialAccountApplication
    {
        if ($this->officialAccount) {
            return $this->officialAccount;
        } else {
            if (config('easywechat.open_platform')) {
                return $this->officialAccount = $this->getOpenPlatform()->getOfficialAccountWithRefreshToken($appId, $this->getAuthorzerRefreshToken($appId));
            } else {
                return $this->officialAccount = app('easywechat.official_account');
            }
        }
    }

    /**
     * 获取小程序实例
     */
    public function getMiniProgram(string $appId): MiniAppApplication
    {
        if ($this->miniProgram) {
            return $this->miniProgram;
        } else {
            if (config('easywechat.open_platform')) {
                return $this->miniProgram = $this->getOpenPlatform()->getMiniAppWithRefreshToken($appId, $this->getAuthorzerRefreshToken($appId));
            } else {
                return $this->miniProgram = app('easywechat.mini_program');
            }
        }
    }

    /**
     * 获取公众号实例
     */
    public function getOpenPlatform(): OpenPlatformApplication
    {
        if ($this->openPlatform) {
            return $this->openPlatform;
        } else {
            return $this->openPlatform = app('easywechat.open_platform');
        }
    }

    /**
     * 获取 RefreshToken
     */
    public function getAuthorzerRefreshToken(string $appId): string
    {
        $cacheKey = sprintf('open-platform.authorizer_refresh_token.%s', $appId);
        $app = $this->getOpenPlatform();
        $cache = $app->getCache();

        $authorizerRefreshToken = (string) $cache->get($cacheKey);

        if (!$authorizerRefreshToken) {
            $api = $app->getClient();
            $response = $api->postJson('/cgi-bin/component/api_get_authorizer_info', [
                'component_appid' => $app->getAccount()->getAppId(),
                'authorizer_appid' => $appId
            ]);
            $authorizerRefreshToken = (string) $response['authorization_info']['authorizer_refresh_token'];
            $cache->set($cacheKey, $authorizerRefreshToken);
        }

        return $authorizerRefreshToken;
    }

    /**
     * 获取 RefreshToken
     */
    public function setAuthorzerRefreshToken(string $appId, string $authorizerRefreshToken): string
    {
        $cacheKey = sprintf('open-platform.authorizer_refresh_token.%s', $appId);
        $app = $this->getOpenPlatform();
        $cache = $app->getCache();

        $cache->set($cacheKey, $authorizerRefreshToken);

        return $authorizerRefreshToken;
    }
}
