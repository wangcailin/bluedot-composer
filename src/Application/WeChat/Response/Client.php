<?php

namespace Composer\Application\WeChat\Response;

use Composer\Application\WeChat\WeChat;
use Composer\Application\WeChat\Response\Handler\EventMessageHandler;
use Composer\Application\WeChat\Response\Handler\LogMessageHandler;
use Composer\Application\WeChat\Response\Handler\TextMessageHandler;

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
