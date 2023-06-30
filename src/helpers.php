<?php

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


if (!function_exists('desensitize')) {
    /**
     * 数据脱敏
     * @param $string 需要脱敏值
     * @return string
     */
    function desensitize($str)
    {
        // 判断是手机号还是邮箱
        if (preg_match('/^1[3-9]\d{9}$/', $str)) {
            // 手机号脱敏
            return substr($str, 0, 3) . '****' . substr($str, 7);
        } elseif (preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $str)) {
            // 邮箱脱敏
            $parts = explode('@', $str);
            $name = $parts[0];
            $domain = $parts[1];
            if (strlen($name) <= 4) {
                // 名称长度小于等于 4，全部脱敏
                $name = '****';
            } else {
                // 名称长度大于 4，前 3 位显示，后面全部脱敏
                $name = substr($name, 0, 3) . '****';
            }
            return $name . '@' . $domain;
        }
        return $str;
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
        $path = $dir . "/" . date('ymd') . '/';
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
        $html  = preg_match_all(' /htt(ps|p):\/\/(statics|img).xiumi.us([^"]*?)(.jpg|.JPG|.png|.PNG|.bmp|.BMP|.gif|.GIF|.jpeg|.JPEG|.webp|.WEBP|.svg|.SVG)/', $content, $LIST);
        foreach ($LIST[0] as $k => $value) {
            $ext = explode('.', $value);
            $dir = !empty($dir) ? $dir : 'XiumiEdit';
            $path = $dir . "/" . date('ymd') . '/';
            $name = $path . md5(rand(1000, 90000) . time()) . '.' . end($ext);
            $new  = Composer\Support\Aliyun\OssClient::putObject($name, file_get_contents($value));
            $content = str_replace($value, $new['info']['url'], $content);
        }
        return $content;
    }
}
if (!function_exists('gmt_iso8601')) {
    function gmt_iso8601($time)
    {
        return str_replace('+00:00', '.000Z', gmdate('c', $time));
    }
}

if (!function_exists('get_excel_column_name')) {
    /**
     * 获取Excel列名字
     *
     * @param integer $num 列数
     * @return string
     */
    function get_excel_column_name(int $num): string
    {
        $numeric = $num % 26;
        $letter = chr(65 + $numeric);
        $num2 = intval($num / 26);
        if ($num2 > 0) {
            return get_excel_column_name($num2 - 1) . $letter;
        } else {
            return $letter;
        }
    }
}
