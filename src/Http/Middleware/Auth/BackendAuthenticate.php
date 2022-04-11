<?php

namespace Composer\Http\Middleware\Auth;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;

class BackendAuthenticate
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if ($this->auth->guard('backend')->guest()) {
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
