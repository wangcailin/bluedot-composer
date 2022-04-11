<?php

namespace Composer\Application\WeChat;

use Composer\Application\User\Models\UserWeChat;
use Composer\Application\WeChat\User\MiniProgram;
use Composer\Application\WeChat\User\OfficialAccount;
use Composer\Http\BaseController;
use Illuminate\Http\Request;

class AuthClient extends BaseController
{

    public function oauth(WeChat $weChat, Request $request)
    {
        $appid = $request->input('appid');
        $redirectUrl = $request->input('redirect_url');
        $code = $request->input('code');
        $app = $weChat->getOfficialAccount($appid);

        if (!$code) {
            $oauthRedirectUrl = $app->oauth->scopes(['snsapi_userinfo'])
                ->redirect($request->getScheme() . '://' . $request->getHost() . '/api/platform/wechat/auth/oauth?appid=' . $appid . '&redirect_url=' . $redirectUrl);
            return \redirect($oauthRedirectUrl);
        } else {
            $user = $app->oauth->scopes(['snsapi_userinfo'])->userFromCode($code)->getRaw();
            $link = '?';
            if (strpos($redirectUrl, '?')) {
                $link = '&';
            }
            $this->oauthAfter($appid, $user);
            $query = http_build_query($user);
            return \redirect($redirectUrl . $link . $query);
        }
    }

    public function auth(WeChat $weChat, Request $request)
    {
        $appid = $request->input('appid');
        $code = $request->input('code');
        $app = $weChat->getMiniProgram($appid);

        $user = $app->auth->session($code);
        $userInfo = $this->oauthAfter($appid, $user);
        $userInfo['auth'] = $user;
        return $this->success($userInfo);
    }

    protected function oauthAfter($appid, $user)
    {
        new OfficialAccount('oauth', null, $appid, $user['openid'], $user);
    }

    protected function authAfter($appid, $user)
    {
        $app = new MiniProgram($appid, $user['unionid'], $user['openid']);
        return $app->getUser();
    }
}
