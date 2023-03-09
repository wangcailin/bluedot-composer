<?php

namespace Composer\Application\WeChat\Response\Handler;

use Illuminate\Support\Facades\Log;

class LogMessageHandler
{
    public $appid;
    public $app;
    public function __construct($appid, $app)
    {
        $this->appid = $appid;
        $this->app = $app;
    }

    public function __invoke($message, \Closure $next)
    {
        $payload = $message->toArray();
        Log::info($payload);
        return $next($message);
    }
}
