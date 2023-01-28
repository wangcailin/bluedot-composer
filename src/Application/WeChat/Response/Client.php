<?php

namespace Composer\Application\WeChat\Response;

use Composer\Application\WeChat\WeChat;
use Composer\Application\WeChat\Response\Handler\EventMessageHandler;
use Composer\Application\WeChat\Response\Handler\LogMessageHandler;
use Composer\Application\WeChat\Response\Handler\TextMessageHandler;

class Client
{
    public $weChat;

    public function __construct(WeChat $weChat)
    {
        $this->weChat = $weChat;
    }

    public function response($appid = '')
    {
        /**
         * 全网发布
         */
        if ($appid == 'wx570bc396a51b8ff8') {
            return $this->releaseToNetwork($appid);
        }
        $app = $this->weChat->getOfficialAccount($appid);
        $server = $app->getServer();

        $server->with(new LogMessageHandler($appid, $app)); // 日志
        $server->addMessageListener('event', new EventMessageHandler($appid, $app)); // 事件消息
        $server->addMessageListener('text', new TextMessageHandler($appid, $app)); // 文本消息
        $server->addEventListener('subscribe', function ($message, \Closure $next) {
            return '感谢您关注';
        });

        return $server->serve();
    }

    /**
     * 处理全网发布相关逻辑
     * @param $authorizer_appid
     * @return mixed
     */
    protected function releaseToNetwork($authorizer_appid)
    {
        $server = $this->weChat->getOfficialAccount($authorizer_appid)->server;

        $server->push(function ($message) {
            switch ($message['MsgType']) {
                case 'text':
                    if (strpos($message['Content'], "QUERY_AUTH_CODE:") !== false) {
                        $auth_code = str_replace("QUERY_AUTH_CODE:", "", $message['Content']);
                        $content = $auth_code . '_from_api';
                        return $content;
                    } elseif ($message['Content'] == 'TESTCOMPONENT_MSG_TYPE_TEXT') {
                        return $message['Content'] . "_callback";
                    }
                    break;
                case 'event':
                    return $message['Event'] . 'from_callback';
                    break;
            }
        });
        return $server->serve()->send();
    }
}
