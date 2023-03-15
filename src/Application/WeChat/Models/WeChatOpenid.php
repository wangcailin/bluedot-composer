<?php

namespace Composer\Application\WeChat\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class WeChatOpenid extends Authenticatable implements JWTSubject
{
    use Notifiable;
    protected $table = 'wechat_openid';

    protected $fillable = [
        'unionid',
        'appid',
        'openid',
        'subscribe',
        'subscribe_time',
        'subscribe_scene',
        'qr_scene',
        'qr_scene_str',
        'nickname',
        'avatar',
    ];

    protected $appends = ['subscribe_scene_text'];

    public function getSubscribeSceneTextAttribute()
    {
        $data = [
            'ADD_SCENE_SEARCH' => '公众号搜索',
            'ADD_SCENE_ACCOUNT_MIGRATION' => '公众号迁移',
            'ADD_SCENE_PROFILE_CARD' => '名片分享',
            'ADD_SCENE_QR_CODE' => '扫描二维码',
            'ADD_SCENE_PROFILE_LINK' => '图文页内名称点击',
            'ADD_SCENE_PROFILE_ITEM' => '图文页右上角菜单',
            'ADD_SCENE_PAID', '支付后关注',
            'ADD_SCENE_WECHAT_ADVERTISEMENT' => '微信广告',
            'ADD_SCENE_OTHERS' => '其他',
        ];

        if ($this->subscribe_scene && !empty($data[$this->subscribe_scene])) {
            return $data[$this->subscribe_scene];
        }
    }

    public function appid()
    {
        return $this->hasOne(Authorizer::class, 'appid', 'appid');
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
