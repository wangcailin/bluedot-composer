<?php

return [
    'aes_iv' => env('AES_IV', ''),
    'aes_key' => env('AES_KEY', ''),

    'rsa_private_key' => env('RSA_PRIVATE_KEY', ''),
    'rsa_public_key' => env('RSA_PUBLIC_KEY', ''),

    'aliyun_access_key_id' => env('ALIYUN_ACCESS_KEY_ID', ''),
    'aliyun_access_key_secret' => env('ALIYUN_ACCESS_KEY_SECRET', ''),

    'aliyun_oss_bucket' => env('ALIYUN_OSS_BUCKET', ''),
    'aliyun_oss_endpoint' => env('ALIYUN_OSS_ENDPOINT', ''),
    'aliyun_sms_sign_name' => env('ALIYUN_SMS_SIGN_NAME', ''),
    'aliyun_sms_template_code' => env('ALIYUN_SMS_TEMPLATE_CODE', ''),
    'aliyun_oss_token_expire' => env('ALIYUN_OSS_TOKEN_EXPIRE', 10),

];
