<?php

if (!function_exists('get_url_json')) {
    /**
     * 获取url JSon数据
     * @param $url
     * @param $params
     * @return mixed
     */
    function get_url_json($url, $params = [])
    {
        $url .= '?' . http_build_query($params);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $res = curl_exec($curl);
        curl_close($curl);
        return json_decode($res, true);
    }
}

if (!function_exists('post_url_json')) {
    /**
     * 发起post请求
     * @param $url
     * @param $data
     * @return mixed
     */
    function post_url_json($url, $data = null)
    {
        //初始化
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        if ($data) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json; charset=utf-8',
            ]);
        }

        $res = curl_exec($curl);
        curl_close($curl);
        return json_decode($res, true);
    }
}

if (!function_exists('html_trans_form')) {
    function html_trans_form($string)
    {
        $string = str_replace('&quot;', '"', $string);
        $string = str_replace('&amp;', '&', $string);
        $string = str_replace('amp;', '', $string);
        $string = str_replace('&lt;', '<', $string);
        $string = str_replace('&gt;', '>', $string);
        $string = str_replace('&nbsp;', ' ', $string);
        $string = str_replace("\\", '', $string);
        return $string;
    }
}


if (!function_exists('data_masking')) {
    /**
     * 数据脱敏
     * @param $string 需要脱敏值
     * @param int $start 开始
     * @param int $length 结束
     * @param string $re 脱敏替代符号
     * @return bool|string
     */
    function data_masking($string, $start = 0, $length = 0, $re = '*')
    {
        if (empty($string)) {
            return false;
        }

        $strarr = [];
        $mb_strlen = mb_strlen($string);

        while ($mb_strlen) { //循环把字符串变为数组
            $strarr[] = mb_substr($string, 0, 1, 'utf8');
            $string = mb_substr($string, 1, $mb_strlen, 'utf8');
            $mb_strlen = mb_strlen($string);
        }

        $strlen = count($strarr);
        $begin = $start >= 0 ? $start : ($strlen - abs($start));
        $end = $last = $strlen - 1;
        if ($length > 0) {
            $end = $begin + $length - 1;
        } elseif ($length < 0) {
            $end -= abs($length);
        }
        for ($i = $begin; $i <= $end; $i++) {
            $strarr[$i] = $re;
        }
        if ($begin >= $end || $begin >= $last || $end > $last) {
            return false;
        }
        return implode('', $strarr);
    }
}

if (!function_exists('data_masking_email')) {
    /**
     * 邮箱数据脱敏
     * @param $string 需要脱敏值
     * @param string $re 脱敏替代符号
     * @return bool|string
     */
    function data_masking_email($string, $re = '*')
    {
        if (empty($string)) {
            return false;
        }
        if (strpos($string, '@') === false) {
            return $string;
        }
        list($name, $doman) = explode('@', $string);
        return data_masking($name, 1, 0, $re) . '@' . $doman;
    }
}
if (!function_exists('file_upload_oss')) {
    /**
     * @param string $url 网络图片地址
     * @param string $dir oss文件夹
     * @return mixed
     */
    function file_upload_oss(string $url, string $dir)
    {
        $ext = explode('.', $url);
        $dir = !empty($dir) ? $dir : 'XiumiEdit';
        $path = $dir."/" . date('ymd') . '/';
        $name = $path . md5(rand(1000, 90000) . time()) . '.' . end($ext);
        $res = Composer\Support\Aliyun\OssClient::putObject($name, file_get_contents($url));
        return $res;
    }
}

if (!function_exists('content_xiumiedit_oss')) {
    /**
     * @param string $content 内容的单引号 都换成双引号
     * @return array|string|string[]
     */
    function content_xiumiedit_oss(string $content)
    {
        $html  = preg_match_all(' /htt(ps|p):\/\/(statics|img).xiumi.us([^"]*?)(.jpg|.JPG|.png|.PNG|.bmp|.BMP|.gif|.GIF|.jpeg|.JPEG|.webp|.WEBP|.svg|.SVG)/',$content,$LIST);
        foreach ($LIST[0] as $k =>$value){
            $ext = explode('.', $value);
            $dir = !empty($dir) ? $dir : 'XiumiEdit';
            $path = $dir."/" . date('ymd') . '/';
            $name = $path . md5(rand(1000, 90000) . time()) . '.' . end($ext);
            $new  = Composer\Support\Aliyun\OssClient::putObject($name, file_get_contents($value));
            $content = str_replace($value,$new['info']['url'],$content);
        }
        return $content;
    }
}
if (!function_exists('content_xiumiedit_oss')) {
    function gmt_iso8601($time)
    {
        return str_replace('+00:00', '.000Z', gmdate('c', $time));
    }
}
