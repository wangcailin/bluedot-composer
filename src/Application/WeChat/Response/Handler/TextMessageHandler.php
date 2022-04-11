<?php

namespace Composer\Application\WeChat\Response\Handler;

use Composer\Application\WeChat\Models\Reply;
use EasyWeChat\Kernel\Contracts\EventHandlerInterface;
use Illuminate\Support\Facades\Log;

class TextMessageHandler implements EventHandlerInterface
{
    public $appid;
    public $app;
    public function __construct($appid, $app)
    {
        $this->appid = $appid;
        $this->app = $app;
    }

    public function handle($payload = null)
    {

        $where = ['appid' => $this->appid];
        if ($reply = Reply::where('text', $payload['Content'])->where('match', 'EQUAL')->where($where)->first()) {
            return $this->reply($reply['reply_material_id'], $payload['FromUserName']);
        } else if ($reply = Reply::where('text', 'like', "%{$payload['Content']}%")->where('match', 'CONTAIN')->where($where)->first()) {
            return $this->reply($reply['reply_material_id'], $payload['FromUserName']);
        } else if ($reply = Reply::where($where)->where('type', 'msg')->first()) {
            return $this->reply($reply['reply_material_id'], $payload['FromUserName']);
        }
    }

    private function reply($replyMaterialId, $openid)
    {
        return (new ReplyMaterialHandle($replyMaterialId, $openid, $this->app))->handle();
    }
}
