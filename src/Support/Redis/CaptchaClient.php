<?php

namespace Composer\Support\Redis;

use Illuminate\Support\Facades\Redis;
use Composer\Exceptions\ApiErrorCode;
use Composer\Exceptions\ApiException;

class CaptchaClient
{
    public const EMAIL_KEY_PREFIX = 'email:verify:code:action:';
    public const SMS_KEY_PREFIX = 'sms:verify:code:action:';
    public const EXPIRE_SEC = 60 * 5;    // 过期时间间隔
    public const RESEND_SEC = 60;     // 重发时间间隔

    /**
     * 发送手机号验证码
     */
    public static function sendSmsCode($phone, $action)
    {
        $code = rand(100000, 999999); //验证码
        $key = self::SMS_KEY_PREFIX . $action . ':' . $phone;
        self::sendHandleRedis($key, $code);
        return $code;
    }

    public static function verifySmsCode($phone, $action, $code)
    {
        $key = self::SMS_KEY_PREFIX . $action . ':' . $phone;
        return self::verifyHandle($key, $code);
    }

    /**
     * 发送邮箱验证码
     */
    public static function sendEmailCode($email, $action)
    {
        $code = rand(100000, 999999); //验证码
        $key = self::EMAIL_KEY_PREFIX . $action . ':' . $email;
        self::sendHandleRedis($key, $code);
        return $code;
    }

    public static function verifyEmailCode($email, $action, $code)
    {
        $key = self::EMAIL_KEY_PREFIX . $action . ':' . $email;
        return self::verifyHandle($key, $code);
    }

    private static function verifyHandle($key, $code)
    {
        $codeData = Redis::get($key);

        if ($codeData) {
            $codeData = json_decode($codeData, true);
            if ($codeData['code'] == $code) {
                Redis::del($key);
                return true;
            }
        }
        throw new ApiException('验证码不正确', ApiErrorCode::VERIFY_CODE_ERROR);
    }

    private static function sendHandleRedis($key, $code)
    {
        self::verifyFreq($key);
        $data = [
            'code' => $code,
            'resend_expire' =>  time() + self::RESEND_SEC
        ];
        Redis::setex($key, self::EXPIRE_SEC, json_encode($data));
    }

    /**
     * 验证码重发限制
     */
    private static function verifyFreq($key)
    {
        $data = json_decode(Redis::get($key), true);
        if ($data && time() < $data['resend_expire']) {
            throw new ApiException('验证码已在1分钟内发出，请耐心等待', ApiErrorCode::VERIFY_CODE_FREQ_ERROR);
        }
    }
}
