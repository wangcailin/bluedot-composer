<?php

namespace Composer\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RequestLog
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $data = [
            'url' => $request->path(),
            'method' => $request->getRealMethod(),
            'content' => $request->all(),
            'ip' => $request->ip(),
            'created_at' => date('Y-m-d H:i:s', time()),
        ];
        Log::info(json_encode($data, JSON_UNESCAPED_UNICODE));
        return $next($request);
    }
}
