<?php

namespace Composer\Application\WeChat\Response\Handler;

use Composer\Application\WeChat\Models\WeChatOpenid;
use Composer\Application\WeChat\Models\WeChatOpenidTagRelation;
use Composer\Application\User\Tag\Tag;
use Composer\Application\WeChat\Models\Qrcode;
use Composer\Application\WeChat\Models\Reply;
use EasyWeChat\Kernel\Contracts\EventHandlerInterface;

class EventMessageHandler implements EventHandlerInterface
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
        switch ($payload['Event']) {
            case 'unsubscribe':
                if ($userOpenid = WeChatOpenid::firstWhere('openid', $payload['FromUserName'])) {
                    $userOpenid->update(['subscribe' => 0]);
                }
                break;
            case 'subscribe':
                if (empty($payload['EventKey'])) {
                    $where = ['appid' => $this->appid];
                    if ($reply = Reply::where($where)->where('type', 'subscribe')->first()) {
                        return $this->reply($reply['reply_material_id'], $payload['FromUserName']);
                    }
                }
                break;
            case 'SCAN':
                $sceneStr = str_replace('qrscene_', '', $payload['EventKey']);
                $qrcode = Qrcode::firstWhere('scene_str', $sceneStr);
                if ($qrcode) {
                    if ($qrcode['tag_ids']) {
                        WeChatOpenidTagRelation::batchCreate($payload['FromUserName'], $qrcode['tag_ids'], []);
                    }
                    return $this->reply($qrcode['reply_material_id'], $payload['FromUserName']);
                }
                break;
        }
    }

    private function reply($replyMaterialId, $openid)
    {
        return (new ReplyMaterialHandle($replyMaterialId, $openid, $this->app))->handle();
    }
}
