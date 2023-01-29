<?php

namespace Composer\Application\WeChat\TemplateMessage\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
        })
            ->everyMinute() // 每分钟执行
            ->withoutOverlapping() // 避免任务重复
            ->onOneServer() // 任务只运行在一台服务器上
            ->runInBackground() // 后台任务
            ->sendOutputTo('./message.log'); // 任务输出
    }
}
