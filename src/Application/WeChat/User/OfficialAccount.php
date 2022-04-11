<?php

namespace Composer\Application\WeChat\User;

class OfficialAccount extends BaseUser
{
    public function __construct($type, $app, $appid, $openid, $wechatUser = [])
    {
        $this->type = $type;
        $this->appid = $appid;
        $this->openid = $openid;
        $this->wechatUser = $wechatUser;

        if (!$wechatUser) {
            $this->wechatUser = $app->user->get($this->openid);
            $this->wechatUser['avatar'] = $this->wechatUser['headimgurl'];
        }

        $this->unionid = $this->wechatUser['unionid'];
        $this->handle();
    }
}
