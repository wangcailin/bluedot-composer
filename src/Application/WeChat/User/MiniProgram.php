<?php

namespace Composer\Application\WeChat\User;

class MiniProgram extends BaseUser
{
    public function __construct($appid, $unionid, $openid, $wechatUser = [])
    {
        $this->appid = $appid;
        $this->unionid = $unionid;
        $this->openid = $openid;
        $this->wechatUser = $wechatUser;
        // $this->handle();
    }
}
