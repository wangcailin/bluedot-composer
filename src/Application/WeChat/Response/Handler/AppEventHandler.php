<?php

namespace Composer\Application\WeChat\Response\Handler;

use Composer\Application\User\Models\User;
use Composer\Application\User\Models\UserWeChatOpenid;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Env;

class AppEventHandler
{
    public static function handler($app, $sceneStr, $openid)
    {
        if (strpos($sceneStr, 'login_event_') !== false) {
            $sceneStrArr = explode('_', $sceneStr);
            $eventId = $sceneStrArr[2];
            $appid = $sceneStrArr[4];
            $unionid = UserWeChatOpenid::where('openid', $openid)->value('unionid');
            $user = User::firstWhere('unionid', $unionid);
            Log::info($user);
            $client = new \WebSocket\Client("wss://middle-platform.blue-dot.cn/api/wss/platform/wechat/qrcode/login?scene_str={$sceneStr}");
            $user['scene_str'] = $sceneStr;
            $client->send(json_encode($user));
            $client->close();
            return '欢迎参加在线研讨会。您可以通过电脑端参与。也可以<a data-miniprogram-appid="' . $appid . '" data-miniprogram-path="/pages/login/index?id=' . $eventId . '&fromType=12" href="https://spmi.blue-dot.cn/html/activity-live?id=' . $eventId . '">点击此处</a>通过手机微信端参与。';
        } elseif (strpos($sceneStr, 'event_poster_') !== false) {
            $sceneStrArr = explode('_', $sceneStr);
            $eventId = $sceneStrArr[2];
            return '<a href="https://spmi.blue-dot.cn/html/activity-live?id=' . $eventId . '">点击此处进入会议直播页>>></a>';
        }
    }
}
