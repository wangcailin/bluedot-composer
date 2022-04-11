<?php

namespace Composer\Http\Middleware;

use Composer\Application\Auth\Models\OperationLog as OperationLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthOperationLog
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle(Request $request, \Closure $next)
    {
        $log = [
            'user_id' => Auth::user()->id,
            'path'    => substr($request->path(), 0, 255),
            'method'  => $request->method(),
            'ip'      => $request->getClientIp(),
            'input'   => json_encode($request->input()),
        ];

        try {
            OperationLog::create($log);
        } catch (\Exception $exception) {
            // pass
        }
        return $next($request);
    }
}
