<?php

namespace Composer\Exceptions;

class ApiErrorCode
{
    public const SUCCESS = 0;

    /**
     * 账户误码
     */
    public const ACCOUNT_LOCK_ERROR = 10101; // 账户锁定
    public const ACCOUNT_DISABLE_ERROR = 10102; // 账户停用
    public const ACCOUNT_EMPTY_ERROR = 10103; // 账户不存在

    public const ACCOUNT_BIND_PHONE_ERROR = 10104; // 绑定手机号失败
    public const ACCOUNT_BIND_EMAIL_ERROR = 10105; // 绑定邮箱失败
    public const ACCOUNT_CHANGE_PASSWORD_ERROR = 10106; // 修改密码失败

    public const ACCOUNT_UNBIND_CODE_ERROR = 10107; // 获取解绑验证码失败

    public const ACCOUNT_LOGIN_ERROR = 10108; // 登录失败
    public const ACCOUNT_REPEAT_ERROR = 10109; // 账户已存在




    /**
     * 验证码错误码
     */
    public const VERIFY_CODE_ERROR = 10201; // 验证码错误
    public const VERIFY_CODE_FREQ_ERROR = 10202; // 验证码超过发送频率

    /**
     * 资源错误码
     */
    public const MODEL_NOT_FOUND_ERROR = 10301; // 资源不存在

    /**
     * 参数错误码
     */
    public const VALIDATION_ERROR = 10401; // 参数错误


    /**
     * 参数错误
     */
    // const VERIFY_CODE_ERROR = 1001; // 验证码错误
    // const VERIFY_CODE_FREQ_ERROR = 1001; // 验证码超过发送频率
}
