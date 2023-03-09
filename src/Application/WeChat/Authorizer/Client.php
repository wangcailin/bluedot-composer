<?php

namespace Composer\Application\WeChat\Authorizer;

use Composer\Application\WeChat\Models\Authorizer;
use Composer\Application\WeChat\WeChat;
use Composer\Http\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Client extends Controller
{
    public function __construct(Authorizer $authorizer)
    {
        $this->model = $authorizer;
    }

    /**
     * 获取授权URL
     */
    public function redirect(Request $request, WeChat $weChat)
    {
        $redirectUrl = $request->input('redirect_url');
        $app = $weChat->getOpenPlatform();

        $url = $app->createPreAuthorizationUrl($redirectUrl);

        return redirect($url);
    }

    /**
     * 授权回调
     */
    public function callback(Request $request, WeChat $weChat)
    {
        $input = $request->validate(['auth_code' => 'required', 'auth_user_id' => 'required']);
        $app = $weChat->getOpenPlatform();
        $authorization = $app->getAuthorization($input['auth_code']);

        $weChat->setAuthorzerRefreshToken(
            $authorization['authorization_info']['authorizer_appid'],
            $authorization['authorization_info']['authorizer_refresh_token']
        );

        $response = $app->getClient()->postJson('/cgi-bin/component/api_get_authorizer_info', [
            'component_appid' => $app->getAccount()->getAppId(),
            'authorizer_appid' => $authorization['authorization_info']['authorizer_appid']
        ]);

        $type = empty($response['authorizer_info']['MiniProgramInfo']) ? 1 : 2;
        $data = [
            'auth_user_id' => $input['auth_user_id'],
            'appid' => $response['authorization_info']['authorizer_appid'],
            'nick_name' => $response['authorizer_info']['nick_name'],
            'user_name' => $response['authorizer_info']['user_name'],
            'head_img' => $response['authorizer_info']['head_img'],
            'type' => $type,
            'subscribe' => true,
            'authorizer_refresh_token' => $authorization['authorization_info']['authorizer_refresh_token']
        ];
        $this->model::updateOrCreate(['appid' => $data['appid']], $data);
        $this->afterCallback($authorization);
    }

    public function afterCallback($authorization)
    {
    }


    /**
     * 授权事件接收URL
     */
    public function event(WeChat $weChat)
    {
        $app = $weChat->getOpenPlatform();
        $server = $app->getServer();

        $server->handleAuthorized(function ($message, \Closure $next) {
            return $next($message);
        });

        $server->handleAuthorizeUpdated(function ($message, \Closure $next) {
            return $next($message);
        });

        $server->handleUnauthorized(function ($message, \Closure $next) {
            $this->model::where('appid', $message['AuthorizerAppid'])->update(['subscribe' => false]);
            return $next($message);
        });

        $server->handleVerifyTicketRefreshed(function ($message, \Closure $next) {
            Log::info($message);
            return $next($message);
        });

        $server->with(function ($message, \Closure $next) {
            return $next($message);
        });

        return $server->serve();
    }
}
