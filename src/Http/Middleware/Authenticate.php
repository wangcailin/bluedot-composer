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
        if ($this->auth->guard($guard)->guest()) {
            throw new ApiException('Unauthorized.', 401);
        }

        return $next($request);
    }
}
