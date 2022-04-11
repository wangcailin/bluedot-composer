<?php

namespace Composer\Application\WeChat\Response\Handler;

use Composer\Application\User\Models\UserWeChatOpenid;
use Composer\Application\WeChat\User\OfficialAccount;

trait Traits
{
    public function getUnionID($payload)
    {
        if (!empty($payload['Event']) && $payload['Event'] == 'unsubscribe') {
            $user = UserWeChatOpenid::firstWhere('openid', $payload['FromUserName']);
            return $user['unionid'];
        } else {
            $OfficialAccount = new OfficialAccount('response', $this->app, $this->appid, $payload['FromUserName']);
            return $OfficialAccount->getUnionID();
        }
    }
}
