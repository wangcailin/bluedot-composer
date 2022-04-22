<?php

namespace Composer\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;

class AuthenticateAdmin
{
    protected $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    public function handle($request, Closure $next, $guard = null)
    {
        if ($this->auth->guard($guard)->user()->is_admin === 1) {
            return $next($request);
        }
        return response('Unauthorized.', 401);
    }
}
