<?php

namespace Composer\Application\User\Group;

use Composer\Http\Controller;
use Composer\Application\User\Models\Group;
use Composer\Application\User\Models\Relation\UserGroup;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\AllowedFilter;

class BackendClient extends Controller
{
    public function __construct(Group $group)
    {
        $this->model = $group;
        $this->validateRules = [
            'name' => ['required', 'string', 'max:20'],
        ];
        $this->allowedFilters = [AllowedFilter::exact('id'), 'name'];
    }

    public function afterBuildFilter()
    {
        $this->model->withCount('users');
    }

    public function getUserSouceCount(UserGroup $userGroup)
    {
        $id = request()->input('id');
        $sum = $userGroup::where('group_id', $id)->count();
        $phone = $userGroup::whereHas('user', function (Builder $query) {
            $query->whereNotNull('phone');
        })->where('group_id', $id)->count();
        $email = $userGroup::whereHas('user', function (Builder $query) {
            $query->whereNotNull('email');
        })->where('group_id', $id)->count();
        $wechat = $userGroup::whereHas('user', function (Builder $query) {
            $query->whereNotNull('unionid');
        })->where('group_id', $id)->count();

        return $this->success(['sum' => $sum, 'phone' => $phone, 'email' => $email, 'wechat' => $wechat]);
    }

    /**
     * 分组合并
     */
    public function merge(Group $group, UserGroup $userGroup)
    {
        $name = request()->input('name');
        $groupID = request()->input('group_ids');
        $newGroup = $group::create(['name' => $name]);
        $userIds = $userGroup::whereIn('group_id', $groupID)->select('user_id')->distinct()->get()->toArray();
        $newGroup->usersRelation()->createMany($userIds);
        return $this->success();
    }
}
