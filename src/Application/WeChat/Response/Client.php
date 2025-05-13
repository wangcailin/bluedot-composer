<?php

namespace BluedotComposer\Application\WeChat\Response;

use BluedotComposer\Application\WeChat\WeChat;
use BluedotComposer\Application\WeChat\Response\Handler\EventMessageHandler;
use BluedotComposer\Application\WeChat\Response\Handler\LogMessageHandler;
use BluedotComposer\Application\WeChat\Response\Handler\TextMessageHandler;

class Client
{
    public function response($appid = '', WeChat $weChat)
    {

        $app = $weChat->getOfficialAccount($appid);
        $server = $app->getServer();

        $server->with(new LogMessageHandler($appid, $app)); // 日志
        $server->addMessageListener('event', new EventMessageHandler($appid, $app)); // 事件消息
        $server->addMessageListener('text', new TextMessageHandler($appid, $app)); // 文本消息

        return $server->serve();
    }
}
