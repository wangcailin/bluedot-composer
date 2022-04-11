<?php

namespace Composer\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class RequestLog
{
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
