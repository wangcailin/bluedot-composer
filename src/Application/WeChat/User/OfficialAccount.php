<?php

namespace Composer\Application\WeChat\User;

class OfficialAccount extends BaseUser
{
    public function __construct($type, $app, $appid, $openid, $wechatUser = [])
    {
        $this->where = ['appid' => $appid, 'openid' => $openid];
        $this->data = [];

        if ($type == 'response') {
            $this->handleResponse($app);
        } else if ($type == 'oauth') {
            $this->handleOauth($wechatUser);
        }

        $this->asyncWeChatOpenid();
    }

    protected function handleResponse($app)
    {
        $wechatUser = $app->user->get($this->openid);
        $this->data = [
            'subscribe' => $wechatUser['subscribe'],
            'subscribe_time' => $wechatUser['subscribe_time'],
            'subscribe_scene' => $wechatUser['subscribe_scene'],
            'qr_scene_str' => $wechatUser['qr_scene_str'],
        ];
        if (isset($wechatUser['unionid'])) {
            $this->data['unionid'] = $wechatUser['unionid'];
        }
    }

    protected function handleOauth($wechatUser)
    {
        $this->data = ['nickname' => $wechatUser['nickname'], 'avatar' => $wechatUser['headimgurl']];
        if (isset($wechatUser['unionid'])) {
            $this->data['unionid'] = $wechatUser['unionid'];
        }
    }
}
