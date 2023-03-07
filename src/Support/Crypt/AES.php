<?php

namespace Composer\Support\Crypt;
use Composer\Exceptions\ApiErrorCode;
use Composer\Exceptions\ApiException;

class AES
{
    /**
     * 解密前端数据然后返回加密数据库数据
     */
    public static function transfromRsaToAes($cryptKey, $value, $config = [])
    {
        $plaintext = AES::decodeRsa($cryptKey, $value);
        $ciphertext = AES::encode($plaintext, $config);
        return $ciphertext;
    }

    public static function decodeRsa($rsa, $str)
    {
        $config = json_decode(Rsa::privateDecrypt($rsa), true);
        return AES::decode($str, $config);
    }

    public static function decode($str, $config = [])
    {
        try {
            $config = self::getConfig($config);
            return openssl_decrypt(base64_decode($str), "AES-256-CBC", $config['key'], OPENSSL_RAW_DATA, $config['iv']);
        } catch (\Exception $exception) {
            // 处理异常
            throw new ApiException('解密失败', ApiErrorCode::VALIDATION_ERROR);
        }
    }

    /**
     * 加密字符串
     * @param string $str 字符串
     * @return string
     */
    public static function encode($str, $config = [])
    {
        try {
            $config = self::getConfig($config);
            return base64_encode(openssl_encrypt($str, "AES-256-CBC", $config['key'], OPENSSL_RAW_DATA, $config['iv']));
        } catch (\Exception $exception) {
            // 处理异常
            throw new ApiException('加密失败', ApiErrorCode::VALIDATION_ERROR);
        }
    }

    private static function getConfig($config)
    {
        if (!isset($config['key'])) {
            $config['key'] = config('composer.aes_key');
        }
        if (!isset($config['iv'])) {
            $config['iv'] = config('composer.aes_iv');
        }
        return $config;
    }
}
