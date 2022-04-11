<?php

namespace Composer\Application\User\Models;

use Composer\Support\AES;
use Composer\Application\Tag\Models\Tag;
use Composer\Application\User\Models\Relation\UserGroup;
use Composer\Application\User\Models\Relation\UserTag;
use Composer\Application\User\Models\Relation\UserTagCount;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'user';

    protected $fillable = [
        'phone',
        'email',
        'unionid',
        'openid',
        'life_cycle',
    ];

    public function info()
    {
        return $this->hasOne(UserInfo::class, 'user_id', 'id');
    }

    public function wechat()
    {
        return $this->hasOne(UserWeChat::class, 'unionid', 'unionid');
    }

    public function wechatOpenid()
    {
        return $this->hasMany(UserWeChatOpenid::class, 'unionid', 'unionid');
    }

    public function group()
    {
        return $this->belongsToMany(Group::class, UserGroup::class, 'user_id', 'group_id')->withTimestamps();
    }

    public function tag()
    {
        return $this->belongsToMany(Tag::class, UserTag::class, 'user_id', 'tag_id')->withTimestamps();
    }

    public function tagCount()
    {
        return $this->hasMany(UserTagCount::class, 'user_id', 'id');
    }

    public function getPhoneAttribute($value)
    {
        return AES::decode($value);
    }
    public function getEmailAttribute($value)
    {
        return AES::decode($value);
    }

    public function scopeSubscribeTime(Builder $query, $startDate, $endDate): Builder
    {
        if ($startDate == $endDate) {
            $endDate = date('Y-m-d', strtotime('+1 day', strtotime($startDate)));
        }
        return $query->whereBetween('subscribe_time', [strtotime($startDate), strtotime($endDate)]);
    }

    public function scopeFilter(Builder $query, $filter): Builder
    {
        $conditions = [
            'AND' => 'where',
            'OR' => 'orWhere',
        ];
        $relationConditions = [
            'AND' => 'whereHas',
            'OR' => 'orWhereHas',
        ];
        foreach ($filter as $key => $value) {
            switch ($key) {
                case 'contacter':
                    foreach ($value as $k => $v) {
                        if (
                            $v &&
                            !empty($v['value']) &&
                            !empty($v['conditions']) &&
                            !empty($v['where'])
                        ) {
                            if (in_array($k, ['phone', 'email'])) {
                                $v['value'] = AES::encode($v['value']);
                            }
                            $where = $this->getWhere($v, $k);
                            if ($where) {
                                call_user_func_array([$query, $conditions[$v['conditions']]], $where);
                            }
                        }
                    }
                    // no break
                case 'info':
                    $this->oneToOne($value, $query, $relationConditions, $conditions, 'info');
                    break;
                case 'wechat':
                    $this->oneToOne($value, $query, $relationConditions, $conditions, 'wechat');
                    break;
                case 'tag':
                    $n = [
                        'AND' => 'whereDoesntHave',
                        'OR' => 'orWhereDoesntHave',
                    ];
                    foreach ($value as $k => $v) {
                        if (
                            $v &&
                            !empty($v['value']) &&
                            !empty($v['conditions']) &&
                            !empty($v['where'])
                        ) {
                            switch ($v['where']) {
                                case 'IN':
                                    call_user_func_array([$query, $relationConditions[$v['conditions']]], ['tagCount', function (Builder $query) use ($v) {
                                        return $query->whereIn('tag_id', $v['value']);
                                    }]);
                                    break;
                                case 'NOT_IN':
                                    call_user_func_array([$query, $relationConditions[$n['conditions']]], ['tagCount', function (Builder $query) use ($v) {
                                        return $query->whereNotIn('tag_id', $v['value']);
                                    }]);
                                    break;
                                case 'EQUAL':
                                    call_user_func_array([$query, $relationConditions[$v['conditions']]], ['tagCount', function (Builder $query) use ($v) {
                                        foreach ($v['value'] as $k => $v) {
                                            $query->whereIn('tag_id', [$v]);
                                        }
                                        return $query;
                                    }]);
                                    break;
                            }
                        }
                    }
                    break;
                case 'life_cycle':
                    if ($value) {
                        $query->whereIn('life_cycle', $value);
                    }
                    break;
            }
        }
        return $query;
    }

    private function getWhere($v, $k)
    {
        $where = [];
        switch ($v['where']) {
            case 'EQUAL':
                $where = [$k, $v['value']];
                break;
            case 'NOT_EQUAL':
                $where = [$k, '<>', $v['value']];
                break;
            case 'CONTAIN':
                $where = [$k, 'ilike', '%' . $v['value'] . '%'];
                break;
            case 'NOT_CONTAIN':
                $where = [$k, 'not ilike', '%' . $v['value'] . '%'];
                break;
            case 'STARTWITH':
                $where = [$k, 'ilike', $v['value'] . '%'];
                break;
            case 'ENDWITH':
                $where = [$k, 'ilike', '%' . $v['value']];
                break;
        }
        return $where;
    }

    private function oneToOne($value, $query, $relationConditions, $conditions, $relationName)
    {
        foreach ($value as $k => $v) {
            if (
                $v &&
                !empty($v['value']) &&
                !empty($v['conditions']) &&
                !empty($v['where'])
            ) {
                $where = $this->getWhere($v, $k);
                call_user_func_array([$query, $relationConditions[$v['conditions']]], [$relationName, function (Builder $query) use ($conditions, $v, $where) {
                    call_user_func_array([$query, $conditions[$v['conditions']]], $where);
                }]);
            }
        }
    }
}
