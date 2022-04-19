<?php

namespace Composer\Application\WeChat\OfficialAccount;

use Composer\Application\WeChat\WeChat;
use Illuminate\Http\Request;
use Composer\Http\BaseController;

class JssdkClient extends BaseController
{
    public function get(Request $request, WeChat $weChat)
    {
        $appid = $request->input('appid');
        $referer_url = $request->input('referer_url');
        $jsApiList = $request->input('js_api_list');
        $app = $weChat->getOfficialAccount($appid);
        $app->jssdk->setUrl(urldecode($referer_url));

        $jsApiList = explode(',', $jsApiList);

        $result = $app->jssdk->buildConfig($jsApiList, $debug = true, $beta = false, $json = false, $openTagList = []);
        return $this->success($result);
    }
}
