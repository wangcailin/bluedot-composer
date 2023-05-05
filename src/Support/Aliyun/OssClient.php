<?php

namespace Composer\Support\Aliyun;

use Illuminate\Http\Request;
use Illuminate\Support\Env;
use OSS\OssClient as BaseOssClient;

class OssClient
{
    public static function getUploadUrl($dir)
    {
        $id = config('composer.aliyun_access_key_id');          // 填写您的AccessKey ID。
        $key = config('composer.aliyun_access_key_secret');     // 填写您的AccessKey Secret。

        // $host的格式为https://bucketname.endpointx，请替换为您的真实信息。
        $host = 'https://' . config('composer.aliyun_oss_bucket') . '.' . config('composer.aliyun_oss_endpoint');

        $now = time();
        $expire = 60 * 60;  //设置该policy超时时间是10s. 即这个policy过了这个有效时间，将不能访问。
        $end = $now + $expire;
        $expiration = gmt_iso8601($end);

        //最大文件大小.用户可以自己设置
        $condition = array(0 => 'content-length-range', 1 => 0, 2 => 1048576000);
        $conditions[] = $condition;

        // 表示用户上传的数据，必须是以$dir开始，不然上传会失败，这一步不是必须项，只是为了安全起见，防止用户通过policy上传到别人的目录。
        $start = array(0 => 'starts-with', 1 => '$key', 2 => $dir);
        $conditions[] = $start;


        $arr = array('expiration' => $expiration, 'conditions' => $conditions);
        $policy = json_encode($arr);
        $base64_policy = base64_encode($policy);
        $string_to_sign = $base64_policy;
        $signature = base64_encode(hash_hmac('sha1', $string_to_sign, $key, true));

        $response = [
            'accessId' => $id,
            'host' => $host,
            'policy' => $base64_policy,
            'signature' => $signature,
            'expire' => $end,
            'dir' =>  $dir . date('Ym'),
        ];
        return $response;
    }
}
