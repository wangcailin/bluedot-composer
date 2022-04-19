<?php

namespace Composer\Application\Event\Chat;

use Composer\Application\Event\Models\Chat;
use Composer\Application\Event\Models\Chat\Like;
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
        Gateway::joinGroup($clientId, "event_{$request['get']['event_id']}");
    }

    /**
     * 当客户端发来消息时触发
     * @param int $client_id 连接id
     * @param mixed $message 具体消息
     */
    public static function onMessage($clientId, $message)
    {
        $data = json_decode($message, true);
        switch ($data['state']) {
            case 0:
                Chat::create($data);
                break;
            case 1:
                Chat::where('id', $data['id'])->update(['state' => 1, 'data' => $data['data']]);
                Gateway::sendToGroup("event_{$data['event_id']}", json_encode($data));
                break;
            case 2:
                Chat::where('id', $data['id'])->update(['state' => 2]);
                break;
            case 3:
                Chat::where('id', $data['id'])->update(['state' => 0]);
                Gateway::sendToGroup("event_{$data['event_id']}", json_encode($data));
                break;
            case 4:
                Chat::where('id', $data['event_chat_id'])->increment('like_count');
                $chat = Chat::find($data['event_chat_id']);
                Like::create($data);
                $data['like_count'] = $chat['like_count'];
                Gateway::sendToGroup("event_{$data['event_id']}", json_encode($data));
                break;
            case 5:
                Chat::where('id', $data['event_chat_id'])->decrement('like_count');
                $chat = Chat::find($data['event_chat_id']);
                Like::where(['event_chat_id' => $data['event_chat_id'], 'user_id' => $data['user_id']])->delete();
                $data['like_count'] = $chat['like_count'];
                Gateway::sendToGroup("event_{$data['event_id']}", json_encode($data));
                break;
        }
    }

    public static function onClose($clientId)
    {
    }
}
