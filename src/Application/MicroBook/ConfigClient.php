<?php

namespace Composer\Application\MicroBook;

use Composer\Application\MicroBook\Models\MicroBookConfig;
use Composer\Http\Controller;
use Composer\Application\WeChat\WeChat;
use Composer\Application\WeChat\Models\Authorizer;
use Composer\Application\Auth\Models\UserToken;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;

class ConfigClient extends Controller
{
    public $app;
    public function __construct(MicroBookConfig $microBookConfig, WeChat $weChat)
    {
        $this->app = $weChat;
        $this->model = $microBookConfig;
        $this->allowedFilters = ['id', 'name',  'remark'];
    }

    public function create()
    {
        $appid = request()->input('appid');
        Authorizer::where('appid', $appid)->update(['app_type' => 1]);

        $app = $this->app->getMiniProgram($appid);

        $params = $this->checkDomainUrl('modify_domain');
        $app->domain->modify($params);

        $params = $this->checkDomainUrl('webviewdomain');
        $app->domain->setWebviewDomain($params);
        return $this->success();
    }

    public function getMiniProgram()
    {
        $where = [
            'app_type' => 1
        ];
        $authorizer = Authorizer::firstWhere($where);
        if ($authorizer) {
            $app = $this->app->getMiniProgram($authorizer['appid']);
            $data = [
                'audit' => $app->code->getLatestAuditStatus(),
                'value' => $this->model->first()
            ];
            return $this->success(['code' => 1, 'data' => $data]);
        }
        return $this->success(['code' => 0]);
    }

    /**
     * 发布小程序
     */
    public function release()
    {
        $authorizer = Authorizer::firstWhere(['app_type' => 1]);
        $app = $this->app->getMiniProgram($authorizer['appid']);
        $app->code->release();
        return $this->success();
    }

    public function sync(Request $request)
    {
        $value = $request->input('value');
        $this->model->updateOrCreate(['value' => $value]);
        $authorizer = Authorizer::firstWhere([
            'app_type' => 1
        ]);
        $token = UserToken::first();
        $ext = $this->miniprogramConfigExt($authorizer, $value, $token['token']);
        $data = [
            'template_id' => env('MICROBOOK_TEMPLATE_ID', 1),
            'ext_json' => $ext,
            'version' => 'v' . env('MICROBOOK_TEMPLATE_ID', 1),
            'desc' => ''
        ];
        $app = $this->app->getMiniProgram($authorizer['appid']);
        $result = $app->code->commit($data['template_id'], $data['ext_json'], $data['version'], $data['desc']);
        if ($result['errcode'] !== 0) {
            return $this->success($result);
        }
        $category = $app->code->getCategory();
        if ($result['errcode'] !== 0) {
            return $this->success($result);
        }
        $data = [
            'item_list' => [
                [
                    "address"       => "pages/index/index",
                    "tag"           => "资讯 微刊",
                    "first_class"   => $category['category_list'][0]['first_class'],
                    "second_class"  => $category['category_list'][0]['second_class'],
                    "first_id"      => $category['category_list'][0]['first_id'],
                    "second_id"     => $category['category_list'][0]['second_id'],
                    "title"         => $authorizer['nick_name']
                ]
            ]
        ];
        $result = $app->code->submitAudit($data['item_list']);
        if ($result['errcode'] !== 0) {
            return $this->success($result);
        }
        return $this->success(['errcode' => 0]);
    }

    private function miniprogramConfigExt($appData, $value, $token)
    {
        $navcolor = '';
        if ($value['navcolor'] == '#fff') {
            $navcolor = 'black';
        } elseif ($value['navcolor'] == '#000') {
            $navcolor = 'white';
        }
        return '{
  "extEnable": true,
  "extAppid": "' . $appData['appid'] . '",
  "directCommit": false,
  "ext": {
    "token": "' . $token . '",
    "appid": "' . $appData['appid'] . '",
    "account" : {
      "nick_name" : "' . $appData['nick_name'] . '",
      "head_img":"' . $appData['head_img'] . '"
    },
    "color":"' . $value['color']['hex'] . '",
    "template": "' . $value['template'] . '"
  },
  "window": {
    "backgroundTextStyle": "light",
    "navigationBarBackgroundColor": "' . $value['navcolor'] . '",
    "navigationBarTitleText": "' . $value['name'] . '",
    "navigationBarTextStyle": "' . $navcolor . '"
  },
  "networkTimeout": {
    "request": 10000,
    "downloadFile": 10000
  }
}';
    }


    private function checkDomainUrl($type)
    {
        switch ($type) {
            case 'modify_domain':
                return [
                    'action'            => 'set',
                    'requestdomain'     => ['https://middle-platform.blue-dot.cn'],
                    'wsrequestdomain'   => ['wss://middle-platform.blue-dot.cn'],
                    'uploaddomain'      => ['https://middle-platform.blue-dot.cn'],
                    'downloaddomain'    => ['https://middle-platform.blue-dot.cn', 'https://mmbiz.qpic.cn', 'https://wx.qlogo.cn', 'https://thirdwx.qlogo.cn', 'https://middle-platform-api.oss-cn-beijing.aliyuncs.com'],
                ];
            case 'webviewdomain':
                return [
                    "action" => "set",
                    "webviewdomain" => ["https://middle-platform.blue-dot.cn"]
                ];
        }
    }
}
