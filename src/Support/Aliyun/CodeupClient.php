<?php

namespace Composer\Support\Aliyun;

use Composer\Support\Aliyun\BaseClient;
use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;

class CodeupClient
{
    public static $client;
    public function __construct()
    {
        if (!self::$client) {
            BaseClient::client();
            self::$client = AlibabaCloud::roa()
                ->product('codeup')
                ->scheme('https') // https | http
                ->host('codeup.cn-hangzhou.aliyuncs.com')
                ->version('2020-04-14');
        }
    }

    /**
     * 创建文件
     */
    public function createFile($body)
    {
        try {
            $result = self::$client
                ->pathPattern('/api/v3/projects/1712837/repository/files')
                ->method('POST')
                ->options([
                    'query' => [
                        'OrganizationId' => '61b2f0aba9dfa18f9b46a3ad',
                    ],
                ])
                ->body(json_encode($body))
                ->request();
        } catch (ClientException $e) {
            echo $e->getErrorMessage() . PHP_EOL;
        } catch (ServerException $e) {
            echo $e->getErrorMessage() . PHP_EOL;
        }
    }

    /**
     * 创建分支
     */
    public function createBranch($body)
    {
        try {
            $result = self::$client
                ->pathPattern('/api/v3/projects/1712837/repository/branches')
                ->method('POST')
                ->options([
                    'query' => [
                        'OrganizationId' => '61b2f0aba9dfa18f9b46a3ad',
                    ],
                ])
                ->body(json_encode($body))
                ->request();
        } catch (ClientException $e) {
            echo $e->getErrorMessage() . PHP_EOL;
        } catch (ServerException $e) {
            echo $e->getErrorMessage() . PHP_EOL;
        }
    }

    /**
     * 删除分支
     */
    public function deleteBranch($branchName)
    {
        try {
            $result = self::$client
                ->pathPattern('/api/v3/projects/1712837/repository/branches/delete')
                ->method('DELETE')
                ->options([
                    'query' => [
                        'BranchName' => $branchName,
                        'OrganizationId' => '61b2f0aba9dfa18f9b46a3ad',
                    ],
                ])
                ->request();
        } catch (ClientException $e) {
            echo $e->getErrorMessage() . PHP_EOL;
        } catch (ServerException $e) {
            echo $e->getErrorMessage() . PHP_EOL;
        }
    }
}
