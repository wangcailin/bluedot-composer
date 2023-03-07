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
        $api = $this->weChat->getOpenPlatform()->getClient();
        $authorizer = $api->handleAuthorize($request->input('auth_code'));
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

    /**
     * 授权事件接收URL
     */
    public function event()
    {
        $app = $this->weChat->getOpenPlatform();
        $server = $app->getServer();

        $server->handleAuthorized(function ($message, \Closure $next) {
            return $next($message);
        });

        $server->handleAuthorizeUpdated(function ($message, \Closure $next) {
            return $next($message);
        });

        $server->handleUnauthorized(function ($message, \Closure $next) {
            Authorizer::where('appid', $message['AuthorizerAppid'])->update(['subscribe' => false]);
            return $next($message);
        });

        $server->handleVerifyTicketRefreshed(function ($message, \Closure $next) {
            return $next($message);
        });

        $server->handleVerifyTicketRefreshed(function ($message, \Closure $next) {
            return $next($message);
        });

        $server->with(function ($message, \Closure $next) {
            return $next($message);
        });

        return $server->serve();
    }
}
