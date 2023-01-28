<?php

namespace Composer\Application\WeChat;

use Composer\Application\WeChat\Models\Qrcode;
use Composer\Application\WeChat\WeChat;
use Composer\Http\Controller;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;

class QrcodeClient extends Controller
{
    public $weChat;

    public function __construct(WeChat $weChat, Qrcode $qrcode)
    {
        $this->weChat = $weChat;
        $this->model = $qrcode;
        $this->allowedFilters = [
            AllowedFilter::exact('appid'),
            'name',
        ];
    }

    public function performCreate()
    {
        $data = request()->all();

        $sceneStr = substr(md5(uniqid(rand(), 1)), 8, 16);
        $api = $this->weChat->getOfficialAccount($data['appid'])->getClient();
        $response = $this->createQrcode($api, $sceneStr);

        $data['scene_str'] = $sceneStr;
        $data['ticket'] = $response['ticket'];
        $data['url'] = $response['url'];

        $this->data = $data;
        if ($this->authUserId) {
            $this->createAuthUserId();
        }
    }

    public function getSceneStr($scene_str)
    {
        $row = $this->model->firstWhere('scene_str', $scene_str);
        return $this->success($row);
    }

    public function getSceneStrQrcode(Request $request)
    {
        $input = $request->only(['scene_str', 'appid']);
        $api = $this->weChat->getOfficialAccount($input['appid'])->getClient();
        $response = $this->createQrcode($api, $input['scene_str']);
        return $this->success($response);
    }

    protected function createQrcode($api, $sceneStr)
    {
        $response = $api->postJson('/cgi-bin/qrcode/create', [
            'action_name' => 'QR_LIMIT_STR_SCENE',
            'action_info' => [
                'scene' => [
                    'scene_str' => $sceneStr
                ]
            ]
        ]);
        return $response;
    }
}
