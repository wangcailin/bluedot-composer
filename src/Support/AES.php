<?php

namespace Composer\Support;

use Illuminate\Support\Env;

class AES
{
    /**
     * 解密字符串
     * @param string $str 字符串
     * @return string
     */
    public static function decode($str)
    {
        return openssl_decrypt(base64_decode($str), "AES-128-CBC", Env::get('AES_KEY', ''), OPENSSL_RAW_DATA, Env::get('AES_IV', ''));
    }

    /**
     * 加密字符串
     * @param string $str 字符串
     * @return string
     */
    public static function encode($str)
    {
        return base64_encode(openssl_encrypt($str, "AES-128-CBC", Env::get('AES_KEY', ''), OPENSSL_RAW_DATA, Env::get('AES_IV', '')));
    }
}
