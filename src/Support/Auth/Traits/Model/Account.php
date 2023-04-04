<?php

namespace Composer\Support\Auth\Traits\Model;

trait Account
{
    protected static function handleCreateUser($username, $password, $email, $phone, $nickname, $isAdmin)
    {
        $nickname = $nickname !== '' ?: $username;
        return self::create([
            'username' => $username,
            'password' => $password,
            'email' => $email,
            'phone' => $phone,
            'nickname' => $nickname,
            'is_admin' => $isAdmin,
        ]);
    }
    public static function createAdminUser($username, $password, $email = '', $phone = '', $nickname = '')
    {
        return self::handleCreateUser($username, $password, $email, $phone, $nickname, 1);
    }
    public static function createUser($username, $password, $email = '', $phone = '', $nickname = '')
    {
        return self::handleCreateUser($username, $password, $email, $phone, $nickname, 0);
    }
}
