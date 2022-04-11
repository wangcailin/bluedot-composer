<?php

namespace Composer\Application\WeChat\Qrcode\Login;

use \GatewayWorker\Lib\Gateway;

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
        $sceneStr = $request['get']['scene_str'];
        Gateway::joinGroup($clientId, $sceneStr);
    }

    /**
     * 当客户端发来消息时触发
     * @param int $client_id 连接id
     * @param mixed $message 具体消息
     */
    public static function onMessage($clientId, $message)
    {
        $message = json_decode($message, true);
        $sceneStr = $message['scene_str'];
        Gateway::sendToGroup($sceneStr, json_encode($message));
    }

    public static function onClose($clientId)
    {

    }

}
