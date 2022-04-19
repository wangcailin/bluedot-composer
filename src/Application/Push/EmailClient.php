<?php

namespace Composer\Application\Push;

use Composer\Http\Controller;
use Illuminate\Http\Request;
use Composer\Application\Config\System\Models\System;
use Composer\Application\Push\Email\Job;
use Composer\Application\Push\Email\JobTrait;
use Composer\Application\Push\Models\VerifyCode;

class EmailClient extends Controller
{
    use JobTrait;
    public function sendMail(Request $request)
    {
        $recipientList = $request->input('recipient_list', []);
        $to = $request->input('to', []);
        $subject = $request->input('subject', '');
        $body = $request->input('body', '');
        $cc = $request->input('cc', []);

        // 合并老版本
        $toN = [];
        if (is_string($to)) {
            $toN[] = $to; //Add a recipient
        } elseif (is_array($to)) {
            $toN = $to;
        }
        $recipientListN = [];
        if (is_string($recipientList)) {
            $recipientListN[] = $recipientList; //Add a recipient
        } elseif (is_array($recipientList)) {
            $recipientListN = $recipientList;
        }

        $mailConfig = System::first();
        dispatch(new Job($mailConfig['data'], array_merge($toN, $recipientListN), $subject, $body, $cc));
        return $this->success();
    }

    // 发送邮件验证码
    public function getVerifyCode(Request $request)
    {
        $email = $request->input('email');
        $action = $request->input('action');
        $code = mt_rand(100000, 999999);
        $body = '您的验证码是：' . $code;
        $expires_in = 900;

        VerifyCode::create(['type' => 'email', 'value' => $email, 'code' => $code, 'expires_in' => $expires_in, 'action' => $action]);

        $mailConfig = System::first();

        $this->sendMailHandle($mailConfig['data'], $email, '邮箱验证码', $body);
        return $this->success();
    }
}
