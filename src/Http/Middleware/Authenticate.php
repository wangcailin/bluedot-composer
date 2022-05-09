<?php

namespace Composer\Http\Middleware;

use Closure;
use Composer\Exceptions\ApiException;
use Illuminate\Contracts\Auth\Factory as Auth;

class Authenticate
{
    protected $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    public function handle($request, Closure $next, $guard = null)
    {
        if ($token = $request->input('token', null)) {
            $request->headers->set('Authorization', 'Bearer ' . $token);
        }

        if ($this->auth->guard($guard)->guest()) {
            return response()->json([
                'errmsg' => 'Unauthorized.',
                'errcode'  => 401,
            ], 401);
        }
        if ($user = $request->user()) {
            if ($user->is_active == 0) {
                return response()->json([
                    'errmsg' => '此账号已停用，请联系管理员',
                    'errcode'  => 401,
                ], 401);
            }
        }

        return $next($request);
    }
}
