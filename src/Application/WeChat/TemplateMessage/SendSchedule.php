<?php

namespace Composer\WeChat\TemplateMessage;

use App\Models\User\User;
use Composer\Application\WeChat\Models\TemplateMessage;
use Composer\Application\WeChat\Models\TemplateMessageTask;
use Composer\WeChat\TemplateMessage\SendJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Laravel\Lumen\Console\Kernel;

class SendSchedule extends Kernel
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
        $schedule
            ->call(function () {
                Log::info(
                    'SendSchedule start --> '
                );

                $time = date('Y-m-d H:i:00');
                $result = TemplateMessageTask::where([
                    'send_state' => 0,
                    'send_time' => $time,
                ])->get();

                Log::info(
                    'SendSchedule start --> get ' . $time . ' --> ' . count($result)
                );

                TemplateMessageTask::where([
                    'send_state' => 0,
                    'send_time' => $time,
                ])->update(['send_state' => 1]);

                foreach ($result as $key => $value) {
                    if ($value['send_group']) {
                        $userList = User::whereHas('group', function (Builder $builder) use ($value) {
                            $builder->whereIn('id', $value['send_group']);
                        })->get();

                        $appid = TemplateMessage::where('template_id', $value['template_id'])->value('appid');

                        foreach ($userList as $k => $v) {
                            $params = [
                                'appid' => $appid,
                                'params' => [
                                    'touser' => $v['openid'],
                                    'template_id' => $value['template_id'],
                                    'url' => $value['url'] ?: '',
                                    'data' => $value['content'],
                                ],
                            ];
                            dispatch(new SendJob($params));
                        }
                    }
                }
                TemplateMessageTask::where([
                    'send_state' => 1,
                    'send_time' => $time,
                ])->update(['send_state' => 2]);
            })
            ->name('send-template-message')
            ->everyMinute() // 每分钟执行
            // ->withoutOverlapping() // 避免任务重复
            // ->onOneServer() // 任务只运行在一台服务器上
            ->runInBackground() // 后台任务
            ->sendOutputTo('./message.log'); // 任务输出
    }
}
