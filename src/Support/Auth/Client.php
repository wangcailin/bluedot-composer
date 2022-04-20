<?php

namespace Composer\Support\Auth;

use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use Composer\Http\BaseController;

use Composer\Exceptions\ApiErrorCode;
use Composer\Exceptions\ApiException;
use Composer\Support\Crypt\AES;
use Composer\Support\Captcha\Client as CaptchaClient;

abstract class Client extends BaseController
{
    /**
     * 登录失败次数
     */
    public $loginfailCount = 10;
    public $usernameWhere = [];

    public $user;
    public $token;
    public $model;

    public function __construct(User $user)
    {
        $this->model = $user;
    }

    public function login(Request $request)
    {
        $input = $request->all();
        return $this->handleLogin($input);
    }

    private function handleLogin($input)
    {
        switch ($input['type']) {
            case 'account':
                $input['password'] = AES::decodeRsa($input['crypt_key'], $input['password']);
                $this->usernameWhere = ['username' => $input['username']];
                $this->loginfailCount($input['username']);
                if (Hash::check($input['password'], $this->user->password)) {
                    $this->getAccessToken();
                }
                break;
            case 'mail':
                if ($result = $this->checkLogin($input, 'email')) {
                    return $result;
                }
                break;
            case 'mobile':
                if ($result = $this->checkLogin($input, 'phone')) {
                    return $result;
                }
                break;
        }
        if ($this->token) {
            $this->logintime();
            return $this->respondWithToken($this->token);
        } else {
            $this->loginfail();
            throw new ApiException('信息错误,错误10次账号将被锁定24小时', ApiErrorCode::ACCOUNT_LOGIN_ERROR);
        }
    }

    private function checkLogin($input, $type)
    {
        $plaintext = AES::decodeRsa($input['crypt_key'], $input[$type]);

        $ciphertext =  AES::encode($plaintext);

        $this->usernameWhere = [$type => $ciphertext];

        $this->loginfailCount();

        if ($type == 'phone') {
            CaptchaClient::verifySmsCode($plaintext, 'login', $input['code']);
        } elseif ($type == 'email') {
            CaptchaClient::verifyEmailCode($plaintext, 'login', $input['code']);
        }

        $this->getAccessToken();
    }

    /**
     * 失败次数
     */
    private function loginfailCount($username = '')
    {
        if ($this->usernameWhere) {
            $this->user = $this->model->firstWhere($this->usernameWhere);
            if (!$this->user) {
                $email = $phone = AES::encode($username);
                if ($this->user = $this->model->firstWhere('phone', $phone)) {
                    $this->usernameWhere = ['phone' => $phone];
                } elseif ($this->user = $this->model->firstWhere('email', $email)) {
                    $this->usernameWhere = ['email' => $email];
                }
            }
        }

        if ($this->user) {
            if ($this->user['loginfail_count'] >= $this->loginfailCount) {
                $time = time();
                $loginfailTime = strtotime($this->user['loginfail_time']);
                $hours = floor(($time - $loginfailTime) / 60 / 60);
                if ($hours < 24) {
                    throw new ApiException('账户已锁定，请在' . (24 - $hours) . '小时后重新登录', ApiErrorCode::ACCOUNT_LOCK_ERROR);
                }
                $this->user->update(['loginfail_count' => 0]);
            }
            if ($this->user['is_active'] == 0) {
                throw new ApiException('此账号已停用，请联系管理员', ApiErrorCode::ACCOUNT_DISABLE_ERROR);
            }
        } else {
            throw new ApiException('用户不存在，请重新输入', ApiErrorCode::ACCOUNT_EMPTY_ERROR);
        }
    }

    /**
     * 失败次数
     */
    private function loginfail()
    {
        if ($this->usernameWhere) {
            $this->model->firstWhere($this->usernameWhere)->increment('loginfail_count');
            $this->model->firstWhere($this->usernameWhere)->update(['loginfail_time' => date('Y-m-d H:i:s')]);
        }
    }

    /**
     * 登录时间
     */
    private function logintime()
    {
        $this->user->update(['logintime' => date('Y-m-d H:i:s'), 'loginfail_count' => 0]);
    }

    /**
     * 获取token
     */
    protected function getAccessToken()
    {
        $this->token = $this->user->createToken('central')->accessToken;
    }

    /**
     * 退出登录
     */
    public function logout()
    {
        if (Auth::check()) {
            Auth::user()->token()->delete();
        }
        $this->success();
    }

    public function currentUser()
    {
        $user = Auth::user();
        $user->append(['mask_phone', 'mask_email']);
        $user['permission'] = $user->getAllPermissions()->pluck('name');
        return $this->success($user);
    }

    public function refresh()
    {
        return $this->respondWithToken(Auth::refresh());
    }

    protected function respondWithToken($token)
    {
        return $this->success([
            'access_token' => $token,
            'X-Tenant' => tenant()->id
        ]);
    }

    public function create(Request $request)
    {
        $data = [
            'nickname' => $request->input('nickname'),
            'username' => $request->input('username'),
            'password' => $request->input('password'),
        ];
        return $this->model->create($data);
    }

    public function getPublicToken()
    {
        $user = Auth::user();
        $token = Auth::tokenById($user->id);
        return $this->success(['access_token' => $token]);
    }

    public function passwordReset(Request $request)
    {
        $user = Auth::user();
        $input = $request->only(['old_password', 'password', 'confirm_password', 'crypt_key']);

        $input['old_password'] = AES::decodeRsa($input['crypt_key'], $input['old_password']);
        if (Hash::check($input['old_password'], $user->password)) {
            if ($input['password'] == $input['confirm_password']) {
                $input['password'] = AES::decodeRsa($input['crypt_key'], $input['password']);
                $user->update(['password' => $input['password']]);
                return $this->success(['code' => 1]);
            }
        }
        throw new ApiException('密码修改失败', ApiErrorCode::ACCOUNT_CHANGE_PASSWORD_ERROR);
    }

    /**
     * 邮箱绑定
     */
    public function bindMail(Request $request)
    {
        $user = Auth::user();
        if ($user['email']) {
            throw new ApiException('已经绑定', ApiErrorCode::ACCOUNT_BIND_PHONE_ERROR);
        }
        $input = $request->only(['email', 'code', 'crypt_key']);
        $input['email'] = AES::decodeRsa($input['crypt_key'], $input['email']);
        CaptchaClient::verifyEmailCode($input['email'], 'bind', $input['code']);
        $this->model->bindEmail($input['email']);
        return $this->success(['errcode' => 0, 'errmsg' => 'ok']);
    }

    /**
     * 邮箱解绑验证
     */
    public function sendUnbindMail()
    {
        $user = Auth::user();
        if ($user->plaintext_email) {
            CaptchaClient::sendEmailCode($user->plaintext_email, 'unbind');
            return $this->success(['errcode' => 0, 'errmsg' => 'ok']);
        }
        throw new ApiException(' 获取解绑验证码失败', ApiErrorCode::ACCOUNT_UNBIND_CODE_ERROR);
    }
    public function unbindMail(Request $request)
    {
        $code = $request->input('code');
        $user = Auth::user();
        CaptchaClient::verifyEmailCode($user->plaintext_email, 'unbind', $code);
        $user->update(['email' => null]);
        return $this->success(['errcode' => 0, 'errmsg' => 'ok']);
    }

    /**
     * 手机号绑定
     */
    public function bindSms(Request $request)
    {
        $user = Auth::user();
        if ($user['phone']) {
            throw new ApiException('已经绑定', ApiErrorCode::ACCOUNT_BIND_PHONE_ERROR);
        }
        $input = $request->only(['phone', 'code', 'crypt_key']);
        $input['phone'] = AES::decodeRsa($input['crypt_key'], $input['phone']);
        CaptchaClient::verifySmsCode($input['phone'], 'bind', $input['code']);
        $this->model->bindSms($input['phone']);
        return $this->success(['errcode' => 0, 'errmsg' => 'ok']);
    }

    /**
     * 解绑验证
     */
    public function sendUnbindSms()
    {
        $user = Auth::user();
        if ($user->plaintext_phone) {
            CaptchaClient::sendSmsCode($user->plaintext_phone, 'unbind');
            return $this->success(['errcode' => 0, 'errmsg' => 'ok']);
        }
        throw new ApiException(' 获取解绑验证码失败', ApiErrorCode::ACCOUNT_UNBIND_CODE_ERROR);
    }
    public function unbindSms(Request $request)
    {
        $code = $request->input('code');
        $user = Auth::user();
        CaptchaClient::verifySmsCode($user->plaintext_phone, 'unbind', $code);
        $user->update(['phone' => null]);
        return $this->success(['errcode' => 0, 'errmsg' => 'ok']);
    }

    public function bindOpenId()
    {
    }

    public function updatePersonal(Request $request)
    {
        $input = $request->only(['nickname', 'avatar']);
        $user = Auth::user();
        $user->update($input);
        return $this->success();
    }
}
