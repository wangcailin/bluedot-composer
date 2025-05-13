<?php

namespace BluedotComposer\Application\WeChat\OfficialAccount;

use BluedotComposer\Application\WeChat\WeChat;
use Illuminate\Http\Request;
use BluedotComposer\Http\BaseController;

class JssdkClient extends BaseController
{
    public function get(Request $request, WeChat $weChat)
    {
        $appid = $request->input('appid');
        $refererUrl = $request->input('referer_url');
        $jsApiList = $request->input('js_api_list');
        $openTagList = $request->input('open_tag_list', []);
        $debug = $request->input('debug', false);

        $app = $weChat->getOfficialAccount($appid);
        $utils = $app->getUtils();
        $result = $utils->buildJsSdkConfig($refererUrl, explode(',', $jsApiList), $openTagList, $debug);

        return $this->success($result);
    }
}
