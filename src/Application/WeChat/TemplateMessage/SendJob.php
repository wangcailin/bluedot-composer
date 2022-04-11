<?php

namespace Composer\WeChat\TemplateMessage;

use Composer\Application\WeChat\Models\TemplateMessageTaskResult;
use Composer\Application\WeChat\WeChat;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    private $weChat;
    private $templateMessageTask;
    private $params;

    public function __construct($params)
    {
        $this->params = $params;
    }

    public function handle()
    {
        Log::info($this->params);
        $weChat = new WeChat;
        $app = $weChat->getOfficialAccount($this->params['appid']);
        $result = $app->template_message->send($this->params['params']);
        $this->params['result'] = $result;

        TemplateMessageTaskResult::create([
            'openid' => $this->params['params']['touser'],
            'result' => $result,
            'params' => $this->params['params'],
        ]);
    }
}
