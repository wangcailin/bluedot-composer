<?php

namespace Composer\Application\User\Models;

use Illuminate\Database\Eloquent\Model;

class WeChatUser extends Model
{
    protected $table = 'wechat_user';

    protected $fillable = [
        'appid',
        'openid',
        'subscribe',
        'subscribe_time',
        'subscribe_scene',
        'qr_scene',
        'qr_scene_str',
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

    // public function register()
    // {
    //     return $this->hasOne(UserRegister::class, 'user_id', 'id');
    // }
}
