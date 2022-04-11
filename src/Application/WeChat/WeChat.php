<?php

namespace Composer\Application\WeChat;

use Symfony\Component\Cache\Adapter\RedisAdapter;

class WeChat
{
    /** 开放平台 */
    public $openPlatform = null;

    /** 公众号 */
    public $officialAccount = null;

    /** 小程序 */
    public $miniProgram = null;

    /**
     * 获取开放平台Token
     */
    public function getComponentAccessToken()
    {
        return $this->getOpenPlatform()->access_token->getToken()['component_access_token'];
    }

    /**
     * 获取公众号实例
     */
    public function getOfficialAccount($AppID)
    {
        if ($this->officialAccount) {
            return $this->officialAccount;
        } else {
            if (config('wechat.open_platform')) {
                return $this->officialAccount = $this->getOpenPlatform()->officialAccount($AppID, $this->getAuthorzerRefreshToken($AppID));
            } else {
                return $this->officialAccount = $this->getApp('official_account');
            }
        }
    }

    /**
     * 获取小程序实例
     */
    public function getMiniProgram($AppID)
    {
        if ($this->miniProgram) {
            return $this->miniProgram;
        } else {
            if (config('wechat.open_platform')) {
                return $this->miniProgram = $this->getOpenPlatform()->miniProgram($AppID, $this->getAuthorzerRefreshToken($AppID));
            } else {
                return $this->miniProgram = $this->getApp('mini_program');
            }
        }
    }

    /**
     * 获取公众号实例
     */
    public function getOpenPlatform()
    {
        if ($this->openPlatform) {
            return $this->openPlatform;
        } else {
            return $this->openPlatform = $this->getApp('open_platform');
        }
    }

    /**
     * 获取token
     */
    private function getAuthorzerRefreshToken($AppID)
    {
        return $this->getOpenPlatform()->getAuthorizer($AppID)['authorization_info']['authorizer_refresh_token'];
    }

    private function rebindCache($app)
    {
        $client = app('redis')->connection()->client();
        $cache = new RedisAdapter($client);
        $app->rebind('cache', $cache);
    }

    private function getApp($name = '')
    {
        $app = app('wechat.' . $name);
        $this->rebindCache($app);
        return $app;
    }
}
