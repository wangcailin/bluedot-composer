<?php

namespace Composer\Application\WeChat\Response\Handler;

use Composer\Application\Analysis\Models\Monitor;
use Composer\Application\WeChat\User\OfficialAccount;
use EasyWeChat\Kernel\Contracts\EventHandlerInterface;
use Illuminate\Support\Facades\Log;

class LogMessageHandler implements EventHandlerInterface
{
    public $appid;
    public $app;
    public function __construct($appid, $app)
    {
        $this->appid = $appid;
        $this->app = $app;
    }

    /**
     * @param mixed $payload
     */
    public function handle($payload = null)
    {
        Log::info($payload);
        $userObj = new OfficialAccount('response', $this->app, $this->appid, $payload['FromUserName']);
        $user = $userObj->getUser();
        $data = [
            'type' => 2,
            'unionid' => isset($user['unionid']) ? $user['unionid'] : '',
            'openid' => $payload['FromUserName'],
            'wechat_user_name' => $payload['ToUserName'],
            'wechat_appid' => $this->appid,
            'wechat_event_key' => empty($payload['EventKey']) ? '' : str_replace('qrscene_', '', $payload['EventKey']),
            'wechat_event_msg' => empty($payload['Content']) ? (empty($payload['Event']) ? '' : $payload['Event']) : $payload['Content'],
            'wechat_event_type' => $payload['MsgType'],
        ];
        if ($data['wechat_event_msg'] == 'MASSSENDJOBFINISH') {
            $data['wechat_event_data'] = [
                'msg_id' => $payload['MsgID'],
                'status' => $payload['Status'],
                'total_count' => $payload['TotalCount'],
                'send_count' => $payload['SentCount'],
                'error_count' => $payload['ErrorCount'],
                'article_url' => $payload['ArticleUrlResult'],
            ];
            $push = new \Composer\Application\Push\Models\Push();
            $push::where('data->msg->id', $payload['MsgID'])->update([
                'total_count' => $payload['TotalCount'],
                'send_count' => $payload['SentCount'],
                'error_count' => $payload['ErrorCount'],
                'state' => 2,
            ]);
        }
        Monitor::create($data);
    }
}
