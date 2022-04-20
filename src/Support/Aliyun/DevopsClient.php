<?php

namespace Composer\Support\Aliyun;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;

class DevopsClient
{
    public static $client;
    public function __construct()
    {
        if (!self::$client) {
            BaseClient::client();
            self::$client = AlibabaCloud::roa()
                ->product('devops')
                ->scheme('https') // https | http
                ->host('devops.cn-hangzhou.aliyuncs.com')
                ->version('2021-06-25');
        }
    }

    /**
     * 创建文件
     */
    public function startPipelineRun($body)
    {
        try {
            $result = self::$client
                ->pathPattern('/organizations/61b2f0aba9dfa18f9b46a3ad/pipelines/1595095/run')
                ->method('POST')
                ->body(json_encode($body))
                ->request();
        } catch (ClientException $e) {
            echo $e->getErrorMessage() . PHP_EOL;
        } catch (ServerException $e) {
            echo $e->getErrorMessage() . PHP_EOL;
        }
    }
}
