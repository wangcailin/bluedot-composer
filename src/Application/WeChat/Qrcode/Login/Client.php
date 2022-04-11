<?php

namespace Composer\Application\WeChat\Qrcode\Login;

use Composer\Application\WeChat\WeChat;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;

class Client extends Controller
{
    public $weChat;

    public function __construct(WeChat $weChat)
    {
        $this->weChat = $weChat;
    }

    public function get(Request $request)
    {
        $appid = $request->input('appid');
        $sceneStr = $request->input('scene_str');
        $result = $this->weChat->getOfficialAccount($appid)->qrcode->temporary($sceneStr, 86400);
        return response()->json($result);
    }

    public function loginWebsocket()
    {
    }
}
