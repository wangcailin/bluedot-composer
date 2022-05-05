<?php

namespace Composer\Application\Auth;

use Illuminate\Http\Request;
use Composer\Support\Captcha\Client;
use Composer\Support\Crypt\AES;

class CaptchaClient extends Client
{
    /**
     * 获取验证码
     */
    public function getCaptcha(Request $request)
    {
        $input = $request->only(['email', 'phone', 'action', 'crypt_key']);
        if (!empty($input['email'])) {
            $input['email'] = AES::decodeRsa($input['crypt_key'], $input['email']);
            Client::sendEmailCode($input['email'], $input['action']);
        } elseif (!empty($input['phone'])) {
            $input['phone'] = AES::decodeRsa($input['crypt_key'], $input['phone']);
            Client::sendSmsCode($input['phone'], $input['action']);
        }
    }
}
