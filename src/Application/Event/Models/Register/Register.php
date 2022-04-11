<?php

namespace Composer\Application\Event\Models\Register;

use Composer\Support\AES;
use Illuminate\Database\Eloquent\Model;
use Composer\Application\User\Models\User as ModelsUser;
use Composer\Application\Event\Models\UserLooklog;
use Composer\Application\Event\Models\Chat;

class Register extends Model
{
    protected $table = 'event_register';

    protected $fillable = [
        'event_id',
        'source',
        'state',
        'user_id',
        'unionid',
        'email',
        'phone',
        'extend',
        'remark',
    ];

    protected $casts = [
        'extend' => 'json',
    ];

    public function getEmailAttribute($value)
    {
        return AES::decode($value);
    }

    public function getPhoneAttribute($value)
    {
        return AES::decode($value);
    }

    public function user()
    {
        return $this->hasOne(ModelsUser::class, 'id', 'user_id');
    }

    public function getViewTimepcAttribute()
    {
        return UserLookLog::where(['event_id' => $this->event_id, 'user_id' => $this->user_id])->whereNotIn('browser',['MicroMessenger'])->sum('tt');
    }

    public function getViewTimeh5Attribute()
    {
        return UserLookLog::where(['event_id' => $this->event_id, 'user_id' => $this->user_id, 'browser' => 'MicroMessenger'])->sum('tt');
    }

    public function getViewBrowserAttribute()
    {
        $arr = UserLookLog::where(['event_id' => $this->event_id, 'user_id' => $this->user_id])->selectRaw("browser")->get()->toArray();
        $browser = [];
        foreach ($arr as $key => $val) {
            $browser[] = $val['browser'];
        }
        return implode(',', array_unique($browser));
    }

    public function getChatDescAttribute()
    {
        $arr = Chat::where(['event_id' => $this->event_id, 'user_id' => $this->user_id])->selectRaw("data")->get()->toArray();
        $content = [];
        foreach ($arr as $key => $val) {
            $content[] = $val['data'];
        }
        return implode(',', array_unique($content));
    }
}
