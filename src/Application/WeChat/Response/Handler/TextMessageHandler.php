<?php

namespace Composer\Application\WeChat\Response\Handler;

use Composer\Application\WeChat\Models\Reply;

class TextMessageHandler
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
        $where = ['appid' => $this->appid];
        if ($reply = Reply::where('text', $payload['Content'])->where('match', 'EQUAL')->where($where)->first()) {
            return $this->reply($reply['reply_material_id'], $payload['FromUserName']);
        } elseif ($reply = Reply::where('match', 'CONTAIN')->where('strpos(\'' . $payload['Content'] . '\',"text") > 0')->where($where)->first()) {
            return $this->reply($reply['reply_material_id'], $payload['FromUserName']);
        } elseif ($reply = Reply::where($where)->where('type', 'msg')->first()) {
            return $this->reply($reply['reply_material_id'], $payload['FromUserName']);
        }
        return $next($message);
    }

    private function reply($replyMaterialId, $openid)
    {
        return (new ReplyMaterialHandle($replyMaterialId, $openid, $this->app))->handle();
    }
}
