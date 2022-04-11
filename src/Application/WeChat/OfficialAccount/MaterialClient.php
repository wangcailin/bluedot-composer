<?php

namespace Composer\Application\WeChat\OfficialAccount;

use Composer\Application\WeChat\WeChat;
use Composer\Http\BaseController;
use Illuminate\Http\Request;

class MaterialClient extends BaseController
{
    public $weChat;

    public function __construct(WeChat $weChat)
    {
        $this->weChat = $weChat;
    }

    public function getList(Request $request)
    {
        $input = $request->only(['appid', 'type', 'offset', 'count']);
        $app = $this->weChat->getOfficialAccount($input['appid']);
        $list = $app->material->list($input['type'], $input['offset'], $input['count']);
        return $this->success($list);
    }
}
