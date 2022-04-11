<?php

namespace Composer\Application\MicroBook;

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
        $authUserId = $request['get']['auth_user_id'];
        Gateway::joinGroup($clientId, $authUserId);
    }

    /**
     * 当客户端发来消息时触发
     * @param int $client_id 连接id
     * @param mixed $message 具体消息
     */
    public static function onMessage($clientId, $message)
    {
        $message = json_decode($message, true);
        $type = $message['type'];
        $appid = $message['appid'];
        $authUserId = $message['auth_user_id'];
        $article = new ArticleAsync();
        $article->handle($authUserId, $type, $appid);
    }

    public static function onClose($clientId)
    {
    }
}
