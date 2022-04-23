<?php

namespace Composer\Support\Auth\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Passport\HasApiTokens;
use Composer\Support\Auth\Traits\Model\Bind;
use Composer\Support\Auth\Traits\Model\Account;
use Composer\Support\Auth\Traits\Model\Attribute;

class User extends Authenticatable
{
    use Notifiable;
    use HasRoles;
    use HasApiTokens;

    use Bind;
    use Account;
    use Attribute;

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
        'loginfail_time',
        'loginfail_count',
        'logintime',
        'is_admin',
        'is_active',
        'avatar'
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
