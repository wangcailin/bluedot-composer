<?php

namespace Composer\Application\WeChat;

use Composer\Application\WeChat\Models\WeChatOpenid;
use Composer\Http\BaseController;
use Illuminate\Http\Request;

class AuthClient extends BaseController
{
    public function oauth(WeChat $weChat, Request $request)
    {
        $appid = $request->input('appid');
        $redirectUrl = $request->input('redirect_url');
        $scope = $request->input('scope', 'snsapi_userinfo');
        $code = $request->input('code');
        $app = $weChat->getOfficialAccount($appid);
        $oauth = $app->getOAuth();
        if (!$code) {
            $oauthRedirectUrl = $oauth->scopes([$scope])
                ->redirect($request->getScheme() . '://' . $request->getHost() . '/api/platform/wechat/auth/oauth?appid=' . $appid . '&redirect_url=' . $redirectUrl);
            return \redirect($oauthRedirectUrl);
        } else {
            $user = $oauth->userFromCode($code)->getRaw();
            $link = '?';
            if (strpos($redirectUrl, '?')) {
                $link = '&';
            }
            $userInfo = $this->oauthAfter($appid, $user);
            $user['token'] = $userInfo->createToken('openid')->plainTextToken;
            $query = http_build_query($user);
            return \redirect($redirectUrl . $link . $query);
        }
    }

    public function auth(WeChat $weChat, Request $request)
    {
        $appid = $request->input('appid');
        $code = $request->input('code');
        $app = $weChat->getMiniProgram($appid);

        $utils = $app->getUtils();
        $user = $utils->codeToSession($code);

        $userInfo = $this->authAfter($appid, $user);

        return $this->success($userInfo);
    }

    protected function oauthAfter($appid, $user): WeChatOpenid
    {
        if (isset($user['unionid'])) {
            $data['unionid'] = $user['unionid'];
        }
        if (isset($user['headimgurl'])) {
            $data['avatar'] = $user['headimgurl'];
        }
        return WeChatOpenid::updateOrCreate(
            ['appid' => $appid, 'openid' => $user['openid']],
            $data
        );
    }

    protected function authAfter($appid, $user): WeChatOpenid
    {
        if (isset($user['unionid'])) {
            $data['unionid'] = $user['unionid'];
        }
        return WeChatOpenid::updateOrCreate(
            ['appid' => $appid, 'openid' => $user['openid']],
            $data
        );
    }
}
