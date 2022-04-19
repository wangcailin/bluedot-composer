<?php

namespace Composer\Application\WeChat\Authorizer;

use Composer\Application\WeChat\Models\Authorizer;
use Composer\Application\WeChat\WeChat;
use Composer\Http\Controller;
use EasyWeChat\OpenPlatform\Server\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Client extends Controller
{
    public $weChat;

    public function __construct(WeChat $weChat)
    {
        $this->weChat = $weChat;
    }

    /**
     * 获取授权URL
     */
    public function url()
    {
        $url = $this->weChat->getOpenPlatform()->getPreAuthorizationUrl('https://middle-platform.blue-dot.cn/api/backend/wechat/authorizer/callback');
        return $this->success(['url' => $url]);
    }

    /**
     * 授权回调
     */
    public function callback(Request $request)
    {
        $authorizer = $this->weChat->getOpenPlatform()->handleAuthorize($request->input('auth_code'));
        $authorizer = $this->weChat->getOpenPlatform()->getAuthorizer($authorizer['authorization_info']['authorizer_appid']);
        $type = empty($authorizer['authorizer_info']['MiniProgramInfo']) ? 1 : 2;
        $data = [
            'appid' => $authorizer['authorization_info']['authorizer_appid'],
            'nick_name' => $authorizer['authorizer_info']['nick_name'],
            'user_name' => $authorizer['authorizer_info']['user_name'],
            'head_img' => $authorizer['authorizer_info']['head_img'],
            'type' => $type,
            'subscribe' => true,
        ];
        Authorizer::updateOrCreate(['appid' => $data['appid']], $data);
        $this->openAppid($type, $data['appid']);
    }

    public function openAppid($type, $appid)
    {
        if ($type == 1) {
            $account = $this->weChat->getOfficialAccount($appid)->account;
        } elseif ($type == 2) {
            $account = $this->weChat->getMiniProgram($appid)->account;
        }
        $result = $account->getBinding();
        $auther = Authorizer::whereNotNull('open_appid')->first();
        if ($result['errcode'] == 0) {
            if ($auther && $auther['open_appid'] != $result['open_appid']) {
                $account->unbindFrom($result['open_appid']);
                $account->bindTo($auther['open_appid']);
                $this->updateOpenAppid($appid, $auther['open_appid']);
            } else {
                $this->updateOpenAppid($appid, $result['open_appid']);
            }
        } else {
            if ($auther) {
                $result = $auther;
            } else {
                $result = $account->create();
            }
            $account->bindTo($result['open_appid']);
            $this->updateOpenAppid($appid, $result['open_appid']);
        }
    }

    private function updateOpenAppid($appid, $result)
    {
        Authorizer::where(['appid' => $appid])->update(['open_appid' => $result]);
    }

    // public function asyncUser($appid)
    // {
    //     $app = $this->openPlatform->getOfficialAccount($appid);
    //     $result = $app->user->list();
    //     foreach ($result['data']['openid'] as $key => $value) {
    //         $user = $app->user->get($value);
    //         User::create([
    //             'appid' => $appid,
    //             'nickname' => $user['nickname'],
    //             'avatar' => $user['headimgurl'],
    //             'sex' => $user['sex'],
    //             'language' => $user['language'],
    //             'country' => $user['country'],
    //             'province' => $user['province'],
    //             'city' => $user['city'],
    //             'remark' => $user['remark'],
    //             'unionid' => empty($user['unionid']) ? '' : $user['unionid'],
    //             'openid' => $user['openid'],
    //             'subscribe' => $user['subscribe'],
    //             'subscribe_time' => $user['subscribe_time'],
    //             'subscribe_scene' => $user['subscribe_scene'],
    //         ]);
    //     }
    // }

    /**
     * 授权事件接收URL
     */
    public function event()
    {
        $server = $this->weChat->getOpenPlatform()->server;

        // 处理授权成功事件
        $server->push(function ($message) {
            Log::info($message);
        }, Guard::EVENT_AUTHORIZED);

        // 处理授权更新事件
        $server->push(function ($message) {
            Log::info($message);
        }, Guard::EVENT_UPDATE_AUTHORIZED);

        // 处理授权取消事件
        $server->push(function ($message) {
            Log::info($message);
            Authorizer::where('appid', $message['AuthorizerAppid'])->update(['subscribe' => false]);
        }, Guard::EVENT_UNAUTHORIZED);

        // VerifyTicket
        $server->push(function ($message) {
            Log::info($message);
        }, Guard::EVENT_COMPONENT_VERIFY_TICKET);

        $server->push(function ($message) {
            Log::info($message);
        }, Guard::EVENT_THIRD_FAST_REGISTERED);

        return $server->serve();
    }
}
