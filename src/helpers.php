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
