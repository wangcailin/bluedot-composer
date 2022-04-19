<?php

namespace Composer\Application\User;

use Composer\Application\Analysis\Models\Monitor;
use Composer\Application\User\Models\Relation\UserGroup;
use Composer\Application\User\Models\Relation\UserTag;
use Composer\Application\User\Models\User;
use Composer\Application\WeChat\Models\Authorizer;
use Composer\Http\Controller;
use Composer\Application\WeChat\User\Async as UserAsync;
use PhpOffice\PhpSpreadsheet\Reader\Csv;

class BackendClient extends Controller
{
    public function __construct(User $user)
    {
        $this->model = $user;
        $this->allowedIncludes = ['group'];
    }

    public function getList()
    {
        $pageSize = (int) request()->input('pageSize', 10);
        $filter = request()->input('filter', []);
        $this->list = $this->model
            ->filter($filter)
            ->with(['wechat', 'group', 'info'])
            ->withCount(['tagCount'])
            ->paginate($pageSize, ['*'], 'current');
        return $this->success($this->list);
    }

    /**
     * 获取用户分组列表
     */
    public function groupRelation()
    {
        $userID = request()->input('user_id');
        $row = $this->model->with('group')->find($userID);
        return $this->success($row ? $row->group : []);
    }
    public function updateGroupRelation($id)
    {
        $groupIds = request()->input('groupIds');
        UserGroup::where('user_id', $id)->whereNotIn('group_id', $groupIds)->delete();
        foreach ($groupIds as $key => $value) {
            UserGroup::firstOrCreate(['user_id' => $id, 'group_id' => $value]);
        }
    }
    public function deleteGroupRelation($user_id, $group_id)
    {
        UserGroup::where(['user_id' => $user_id, 'group_id' => $group_id])->delete();
    }

    /**
     * 搜索结果分组
     */
    public function searchResultGroup()
    {
        $groupID = request()->input('group_id');
        $filter = request()->input('filter');
        $model = $this->model->filter($filter);
        foreach ($model->cursor() as $user) {
            $data = [
                'user_id' => $user->id,
                'group_id' => $groupID,
            ];
            UserGroup::firstOrCreate($data, $data);
        }
        return $this->success();
    }

    /**
     * 获取用户标签列表
     */
    public function tagRelation()
    {
        $userID = request()->input('user_id');
        $row = $this->model->with('tagCount.tag')->find($userID);
        return $this->success($row ? $row->tagCount : []);
    }
    public function updateTagRelation($id)
    {
        $tagIds = request()->input('tagIds');
        UserTag::where('user_id', $id)->whereNotIn('tag_id', $tagIds)->delete();
        foreach ($tagIds as $key => $value) {
            UserTag::firstOrCreate(['user_id' => $id, 'tag_id' => $value]);
        }
    }
    public function deleteTagRelation($user_id, $tag_id)
    {
        UserTag::where(['user_id' => $user_id, 'tag_id' => $tag_id])->delete();
    }

    public function profile($id)
    {
        $row = $this->model->with(['group', 'wechat.openid.appid', 'info'])->findOrFail($id);
        return $this->success($row);
    }

    public function timeline($id, Monitor $monitor)
    {
        $user = $this->model->find($id);
        $list = $monitor->where('unionid', $user['unionid'])->orderBy('created_at', 'desc')->paginate(10);
        return $this->success($list);
    }

    public function auth($id)
    {
        $list = Authorizer::join('wechat_user_openid', 'wechat_authorizer.appid', '=', 'wechat_user_openid.appid')
            ->join('wechat_user', 'wechat_user.id', '=', 'wechat_user_openid.user_id')
            ->where([
                ['wechat_authorizer.type', '=', request()->input('type', 1)],
                ['wechat_user.id', '=', $id],
            ])
            ->select('wechat_authorizer.*')
            ->paginate(10);
        return $this->success($list);
    }

    public function import(Csv $csv)
    {
        $file = request()->file('file');
        $filename = time() . '.csv';
        $path = storage_path() . '/upload/';
        $file->move($path, $filename);
        return $this->success(['filepath' => $path . $filename, 'count' => 0]);
    }

    public function async(UserAsync $userAsync)
    {
        $userAsync->handle();
        return $this->success();
    }

    public function getStatistic()
    {
        $sum = $this->model->count();
        $subscribe = $this->model->with('wechat.subscribe', 1)->count();
        $register = $this->model
            ->whereNotNull('phone')
            ->whereNotNull('email')
            ->count();
        return $this->success([
            'sum' => $sum,
            'subscribe' => $subscribe,
            'register' => $register,
        ]);
    }
}
