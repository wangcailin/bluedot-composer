<?php

namespace Composer\Application\Push\WeChat\TemplateMessage;

use Composer\Http\Controller;
use Illuminate\Http\Request;

class Client extends Controller
{
    public function send(Request $request)
    {
        $input = $request->only(['appid', 'openid', 'template_id', 'url', 'data']);
        $params = [
            'appid' => $input['appid'],
            'params' => [
                'touser' => $input['openid'],
                'template_id' => $input['template_id'],
                'url' => $input['url'] ?: '',
                'data' => $input['data'],
            ],
        ];
        dispatch(new Job($params));
        return $this->success();
    }
}
