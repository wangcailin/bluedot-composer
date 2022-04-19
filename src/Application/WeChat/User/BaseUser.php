<?php

namespace Composer\Application\WeChat\User;

use Composer\Application\User\Models\User;
use Composer\Application\User\Models\UserWeChat;
use Composer\Application\User\Models\UserWeChatOpenid;

abstract class BaseUser
{
    protected $wechatUser;
    protected $type;
    protected $appid;
    protected $unionid;
    protected $openid;
    public $user;


    protected function handle()
    {
        $this->asyncUser();
        $this->asyncUserWeChat();
        $this->asyncUserWeChatOpenid();
    }

    private function asyncUser()
    {
        $data = [
            'unionid' => $this->unionid,
        ];
        $this->user = User::firstOrCreate($data, $data);
    }

    private function asyncUserWeChat()
    {
        if ($this->wechatUser) {
            $data = [
                'nickname' => $this->wechatUser['nickname'],
                'avatar' => $this->wechatUser['avatar'],
            ];
            if ($this->type == 'response') {
                $data['remark'] = $this->wechatUser['remark'];
            }
            UserWeChat::updateOrCreate(['unionid' => $this->unionid], $data);
        }
    }

    private function asyncUserWeChatOpenid()
    {
        $data = [
            'unionid' => $this->unionid,
            'appid' => $this->appid,
        ];
        if ($this->type == 'response') {
            $data['subscribe'] = $this->wechatUser['subscribe'];
            $data['subscribe_time'] = $this->wechatUser['subscribe_time'];
            $data['subscribe_scene'] = $this->wechatUser['subscribe_scene'];
        }
        UserWeChatOpenid::updateOrCreate(['openid' => $this->openid], $data);
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getUnionid()
    {
        return $this->unionid;
    }
}
