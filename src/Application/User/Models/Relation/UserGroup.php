<?php

namespace Composer\Application\User\Models\Relation;

use Composer\Application\User\Models\User;
use Illuminate\Database\Eloquent\Relations\Pivot;

class UserGroup extends Pivot
{
    protected $table = 'user_group_relation';

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
