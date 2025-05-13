<?php

namespace BluedotComposer\Application\WeChat\OfficialAccount;

use BluedotComposer\Application\WeChat\WeChat;
use Illuminate\Http\Request;
use BluedotComposer\Http\BaseController;

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
        return $this->success($response->toArray());
    }
}
