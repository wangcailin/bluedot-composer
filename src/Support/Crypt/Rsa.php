<?php

namespace Composer\Support\Crypt;

class Rsa
{

    public function __construct()
    {
    }

    public function publicEncrypt($data, $publicKey)
    {
        openssl_public_encrypt($data, $encrypted, $publicKey);
        return $encrypted;
    }

    public function publicDecrypt($data, $publicKey)
    {
        openssl_public_decrypt($data, $decrypted, $publicKey);
        return $decrypted;
    }

    public function privateEncrypt($data, $privateKey)
    {
        openssl_private_encrypt($data, $encrypted, $privateKey);
        return $encrypted;
    }

    public static function privateDecrypt($str)
    {
        openssl_private_decrypt(base64_decode($str), $decrypted, config('composer.rsa_private_key'));
        return $decrypted;
    }
}
