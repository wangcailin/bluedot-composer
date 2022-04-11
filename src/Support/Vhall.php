<?php

namespace Composer\Support;

class Vhall
{
    protected static $APP_ID;
    protected static $SECRET_KEY;
    protected static $baseUrl = 'http://api.vhallyun.com/api/v2/';


    /**
     * 获取推流信息
     */
    public static function getPushInfo($roomId, $expireTime)
    {
        $url = self::$baseUrl . 'room/get-push-info';
        return get_url_json($url, self::publicParams([
            'room_id' => $roomId,
            'expire_time' => $expireTime,
        ]));
    }

    /**
     * 创建直播室
     */
    public static function createRoom()
    {
        $url = self::$baseUrl . 'room/create';
        return get_url_json($url, self::publicParams());
    }

    /**
     * 创建回放
     */
    public static function createRoomVod($streamId, $startTime, $endTime)
    {
        $url = self::$baseUrl . 'vod';
        return get_url_json($url, self::publicParams([
            'action' => 'SubmitCreateRecordTasks',
            'stream_id' => $streamId,
            'start_time' => $startTime,
            'end_time' => $endTime,
        ]));
    }

    /**
     * 获取access_token,默认有效期一天
     */
    public static function getAccessToekn($roomId, $thirdPartyUserId, $liveInavId = null)
    {
        $url = self::$baseUrl . 'base/create-v2-access-token';
        $params = [
            'publish_stream' => $roomId,
            'third_party_user_id' => $thirdPartyUserId,
        ];
        if ($liveInavId) {
            $params['publish_inav_stream'] = $params['publish_inav_another'] = $liveInavId;
        }
        return get_url_json($url, self::publicParams($params));
    }

    private static function getAppID()
    {
        return self::$APP_ID ?: self::$APP_ID = env('VHALL_APP_ID', '');
    }
    private static function getSecretKey()
    {
        return self::$SECRET_KEY ?: self::$SECRET_KEY = env('VHALL_SECRET_KEY', '');
    }

    /**
     * 公共请求参数
     */
    private static function publicParams($params = [])
    {
        $data = array_merge([
            'app_id' => self::getAppID(),
            'signed_at' => (string) time(),
        ], $params);
        $data['sign'] = self::sign($data);
        return $data;
    }

    /**
     * 获取API签名
     * @return string
     */
    private static function sign($params)
    {
        $SECRET_KEY = self::getSecretKey();
        ksort($params);
        $str = '';
        foreach ($params as $k => $v) {
            $str .= $k . $v;
        }
        $str = $SECRET_KEY . $str . $SECRET_KEY;
        return md5($str);
    }
}
