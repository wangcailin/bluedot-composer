<?php

namespace Composer\Application\WeChat\OfficialAccount;

use Composer\Application\WeChat\WeChat;
use Illuminate\Http\Request;
use Composer\Http\BaseController;

class UserClient extends BaseController
{
    public function getInfo(Request $request, WeChat $weChat)
    {
        $appid = $request->input('appid');
        $openid = $request->input('openid');
        $app = $weChat->getOfficialAccount($appid);
        $api = $app->getClient();

        $response = $api->get('/cgi-bin/user/info', [
            'openid' => $openid
        ]);
        return $this->success($response);
    }
}
