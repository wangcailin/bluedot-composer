<?php

namespace Composer\Application\User\Models;

use Composer\Application\User\Models\Relation\UserGroup;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{

    protected $table = 'user_group';

    protected $fillable = [
        'type',
        'name',
        'remark',
    ];

    protected static function boot()
    {
        parent::boot();

        // 在删除用户时执行
        static::deleting(function ($group) {
            // 删除与用户关联的文章
            $group->usersRelation()->delete();
        });
    }

    public function usersRelation()
    {
        return $this->hasMany(UserGroup::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, UserGroup::class);
    }
}
