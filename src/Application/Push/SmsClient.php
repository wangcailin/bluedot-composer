<?php

namespace Composer\Application\Push;

use Composer\Http\Controller;
use Illuminate\Http\Request;
use Composer\Application\Push\Sms\Job;
use Composer\Application\Push\Sms\JobTrait;
use Composer\Application\Push\Models\VerifyCode;

class SmsClient extends Controller
{
    use JobTrait;
    public function sendSms(Request $request)
    {
        $query = $request->input('query');
        dispatch(new Job($query));
        return $this->success();
    }

    // 发送邮件验证码
    public function getVerifyCode(Request $request)
    {
        $phone = $request->input('phone');
        $action = $request->input('action');
        $code = mt_rand(100000, 999999);
        $expires_in = 900;

        VerifyCode::create(['type' => 'sms', 'value' => $phone, 'code' => $code, 'expires_in' => $expires_in, 'action' => $action]);

        $this->sendSmsHandle([
            'PhoneNumbers' => $phone,
            'SignName' => "北京蓝运方小",
            'TemplateCode' => "SMS_222985193",
            'TemplateParam' => '{"code":"' . $code . '"}',
        ]);
        return $this->success();
    }
}
