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
        $api = $this->weChat->getOfficialAccount($input['appid'])->getClient();

        $response = $api->get('/cgi-bin/material/batchget_material', [
            'type' => $input['type'],
            'offset' => $input['offset'],
            'count' => $input['count'],
        ]);

        return $this->success($response);
    }
}
