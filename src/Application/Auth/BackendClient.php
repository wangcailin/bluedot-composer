<?php

namespace Composer\Application\Auth;

use Composer\Support\Auth\Models\User;
use Composer\Application\Push\Traits\VerifyCodeTrait;
use Composer\Http\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class BackendClient extends BaseController
{
    use VerifyCodeTrait;

    use VerifyCodeTrait;

    /**
     * 登录失败次数
     */
    public $loginfailureCount = 10;

    public $user;
    public $token;

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $type = $request->input('type');
        if ($type == 'account') {
            $credentials = $request->only(['username', 'password']);
            $r = $this->loginfailureCount($credentials);
            if ($r !== true) {
                return $this->success(['errcode' => 1, 'type' => $type, 'errmsg' => $r], 200);
            }
            if (Hash::check($credentials['password'], $this->user->password)) {
                $this->getAccessToken();
            }
        } elseif ($type == 'mail') {
            $credentials = $request->only(['email', 'code']);
            $r = $this->loginfailureCount($credentials);
            if ($r !== true) {
                return $this->success(['errcode' => 1, 'type' => $type, 'errmsg' => $r], 200);
            }
            if ($this->verifyCode('email', $credentials['email'], $credentials['code'], 'login')) {
                $this->getAccessToken();
            }
        } elseif ($type == 'mobile') {
            $credentials = $request->only(['phone', 'code']);
            $r = $this->loginfailureCount($credentials);
            if ($r !== true) {
                return $this->success(['errcode' => 1, 'type' => $type, 'errmsg' => $r], 200);
            }
            if ($this->verifyCode('sms', $credentials['phone'], $credentials['code'], 'login')) {
                $this->getAccessToken();
            }
        }
        if ($this->token) {
            $this->logintime();
            return $this->respondWithToken($this->token);
        } else {
            $this->loginfailure($credentials);
            return $this->success(['errcode' => 1, 'type' => $type, 'errmsg' => '信息错误,错误10次账号将被锁定24小时'], 200);
        }
    }

    /**
     * 失败次数
     */
    private function loginfailureCount($credentials)
    {
        $where = [];
        if (!empty($credentials['username'])) {
            $where['username'] = $credentials['username'];
        } elseif (!empty($credentials['email'])) {
            $where['email'] = $credentials['email'];
        } elseif (!empty($credentials['phone'])) {
            $where['phone'] = $credentials['phone'];
        }
        if ($where) {
            $this->user = User::firstWhere($where);
        }

        if ($this->user) {
            if ($this->user['loginfailure_count'] >= $this->loginfailureCount) {
                $time = time();
                $loginfailureTime = strtotime($this->user['loginfailure_time']);
                $hours = floor(($time - $loginfailureTime) / 60 / 60);
                if ($hours < 24) {
                    return '账户已锁定，请在' . (24 - $hours) . '小时后重新登录';
                }
                $this->user->update(['loginfailure_count' => 0]);
            }
            if ($this->user['is_active'] == 0) {
                return '此账号已停用，请联系管理员';
            }
        } else {
            return '用户不存在，请重新输入';
        }
        return true;
    }

    /**
     * 失败次数
     */
    private function loginfailure($credentials)
    {
        $where = [];
        if (!empty($credentials['username'])) {
            $where['username'] = $credentials['username'];
        } elseif (!empty($credentials['email'])) {
            $where['email'] = $credentials['email'];
        } elseif (!empty($credentials['phone'])) {
            $where['phone'] = $credentials['phone'];
        }
        if ($where) {
            User::where($where)->increment('loginfailure_count');
            User::where($where)->update(['loginfailure_time' => date('Y-m-d H:i:s')]);
        }
    }

    /**
     * 登录时间
     */
    private function logintime()
    {
        $this->user->update(['logintime' => date('Y-m-d H:i:s'), 'loginfailure_count' => 0]);
    }

    /**
     * 获取token
     */
    private function getAccessToken()
    {
        $this->token = $this->user->createToken('backend')->accessToken;
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



    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function currentUser()
    {
        $user = Auth::user();
        $user['permission'] = $user->getAllPermissions()->pluck('name');
        return $this->success($user);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(Auth::refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return $this->success([
            'access_token' => $token,
            'token_type' => 'bearer',
            // 'expires_in' => Auth::factory()->getTTL() * 60,
        ]);
    }

    public function create()
    {
        $data = [
            'nickname' => $this->request->input('nickname'),
            'username' => $this->request->input('username'),
            'password' => $this->request->input('password'),
        ];
        return User::create($data);
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
        $input = $request->only(['password', 'confirm_password']);
        if ($input['password'] == $input['confirm_password']) {
            $user->update(['password' => $input['password']]);
            return $this->success(['code' => 1]);
        }
        return $this->success(['code' => 0]);
    }

    /**
     * 邮箱绑定
     */
    public function bindMail(Request $request)
    {
        $email = $request->input('email');
        $code = $request->input('code');
        $action = $request->input('action');
        if ($this->verifyCode('email', $email, $code, $action)) {
            $user = Auth::user();
            if (User::firstWhere('email', $email)) {
                return $this->success(['code' => 2]);
            }
            $user->update(['email' => $email]);
            return $this->success(['code' => 1]);
        }
        return $this->success(['code' => 0]);
    }

    /**
     * 邮箱解绑验证
     */
    public function verifyBindMail(Request $request)
    {
        $email = $request->input('email');
        $code = $request->input('code');
        $action = $request->input('action');
        if ($this->verifyCode('email', $email, $code, $action)) {
            return $this->success(['code' => 1]);
        }
        return $this->success(['code' => 0]);
    }

    /**
     * 邮箱绑定
     */
    public function bindSms(Request $request)
    {
        $phone = $request->input('phone');
        $code = $request->input('code');
        $action = $request->input('action');
        if ($this->verifyCode('sms', $phone, $code, $action)) {
            $user = Auth::user();
            if (User::firstWhere('phone', $phone)) {
                return $this->success(['code' => 2]);
            }
            $user->update(['phone' => $phone]);
            return $this->success(['code' => 1]);
        }
        return $this->success(['code' => 0]);
    }

    /**
     * 邮箱解绑验证
     */
    public function verifyBindSms(Request $request)
    {
        $phone = $request->input('phone');
        $code = $request->input('code');
        $action = $request->input('action');
        if ($this->verifyCode('sms', $phone, $code, $action)) {
            return $this->success(['code' => 1]);
        }
        return $this->success(['code' => 0]);
    }

    public function bindOpenId()
    {
    }
}
