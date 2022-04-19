<?php

namespace Composer\Application\User\Tag;

use Composer\Application\User\Models\User;
use Composer\Application\User\Models\Relation\UserTag;
use Composer\Application\Tag\Models\Tag as TagModel;

class Tag
{
    public static function create($unionid, $tagIds)
    {
        if ($user = User::firstWhere('unionid', $unionid)) {
            if ($tagIds) {
                $tag = TagModel::whereIn('id', $tagIds)->get();
                foreach ($tag as $k => $v) {
                    UserTag::create(['user_id' => $user['id'], 'tag_id' => $v['id']]);
                }
                if ($user['life_cycle'] < 3) {
                    $user->life_cycle = 3;
                    $user->save();
                }
            }
        }
    }
}
