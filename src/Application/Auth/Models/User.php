<?php

namespace Composer\Application\Auth\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable;
    use HasRoles;
    use HasApiTokens;

    protected $table = 'auth_user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nickname',
        'username',
        'password',
        'phone',
        'email',
        'openid',
        'loginfailure_time',
        'loginfailure_count',
        'logintime',
        'is_admin',
        'is_active',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];


    /**
     * 设置密码
     *
     * @param  string  $value
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = \Illuminate\Support\Facades\Hash::make($value);
    }

    private static function handleCreateUser($username, $password, $email = '', $phone = '', $nickname = '', $isAdmin)
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
        return self::handleCreateUser($username, $password, $email = '', $phone = '', $nickname = '', 1);
    }
    public static function createUser($username, $password, $email = '', $phone = '', $nickname = '')
    {
        return self::handleCreateUser($username, $password, $email = '', $phone = '', $nickname = '', 0);
    }
}
