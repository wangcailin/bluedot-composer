<?php

namespace Composer\Application\Event\Live;

use Composer\Application\Event\Models\Live\PPT;
use GatewayWorker\Lib\Gateway;

class WebSocket
{
    /**
     * 当客户端连接时触发
     * 如果业务不需此回调可以删除onConnect
     *
     * @param int $client_id 连接id
     */
    public static function onWebSocketConnect($clientId, $request)
    {
        Gateway::joinGroup($clientId, "event_upload_ppt_{$request['get']['event_id']}");
    }

    /**
     * 当客户端发来消息时触发
     * @param int $client_id 连接id
     * @param mixed $message 具体消息
     */
    public static function onMessage($clientId, $message)
    {
        $data = json_decode($message, true);
        if ($data['type'] == 'active_index') {
            PPT::where('event_id', $data['event_id'])->update(['active_index' => $data['active_index']]);
        }
        Gateway::sendToGroup("event_upload_ppt_{$data['event_id']}", json_encode($data));
    }

    public static function onClose($clientId)
    {
    }
}
