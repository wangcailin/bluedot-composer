<?php

/**
 * 记录请求日志
 */

namespace Composer\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class RequestLog
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $data = [
            'url' => $request->path(),
            'method' => $request->getRealMethod(),
            'content' => $request->all(),
            'ip' => $request->ip(),
            'created_at' => date('Y-m-d H:i:s', time()),
        ];
        Log::info(json_encode($data));
        return $next($request);
    }
}
