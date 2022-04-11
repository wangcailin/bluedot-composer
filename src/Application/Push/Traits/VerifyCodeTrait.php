<?php

namespace Composer\Application\Push\Traits;

use Composer\Application\Push\Models\VerifyCode;

trait VerifyCodeTrait
{
    // 验证验证码
    private function verifyCode($type, $value, $code, $action)
    {
        $row = VerifyCode::firstWhere(['type' => $type, 'value' => $value, 'code' => $code, 'state' => false, 'action' => $action]);
        if ($row) {
            if (strtotime($row['created_at']) + $row['expires_in'] >= time()) {
                VerifyCode::where(['value' => $value, 'code' => $code])->update(['state' => true]);
                return true;
            }
        }
        return false;
    }
}
