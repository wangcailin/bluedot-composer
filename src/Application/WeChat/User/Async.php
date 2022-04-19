<?php

namespace Composer\Application\WeChat\User;

use Composer\Application\WeChat\Models\Authorizer;
use Composer\Application\WeChat\WeChat;

class Async
{
    public $weChat;

    public function __construct(WeChat $weChat)
    {
        $this->weChat = $weChat;
    }

    public function handle()
    {
        $authorizer = Authorizer::where(['type' => 1])->get();
        foreach ($authorizer as $k => $v) {
            $app = $this->weChat->getOfficialAccount($v['appid']);
            $result = $app->user->list();
            foreach ($result['data']['openid'] as $key => $value) {
                // $user = $app->user->get($value);
                new OfficialAccount('async', $app, $v['appid'], $value);
                // OfficialAccount::asyncUser($v['appid'], $user, $authUserID);
            }
        }
    }
}
