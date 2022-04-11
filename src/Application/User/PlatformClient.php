<?php

namespace Composer\Application\User;

use Composer\Application\User\Models\User;
use Composer\Application\User\Models\UserInfo;
use Composer\Http\Controller;
use Illuminate\Http\Request;

class PlatformClient extends Controller
{
    public function __construct(User $user)
    {
        $this->model = $user;
    }

    public function row(Request $request)
    {
        $input = $request->only(['phone', 'email', 'unionid']);
        if (!$input) {
            return $this->fail(1001, '获取用户缺少输入参数phone|email|unionid');
        }

        $row = $this->model->with(['info', 'wechat', 'wechatOpenid'])->firstWhere($input);
        return $this->success($row);
    }

    public function update($id)
    {
        $request = request();
        $row = $this->model::findOrFail($id);
        $input = $request->only(['phone', 'email']);
        if ($input) {
            $updateData = $input;
            if ($row['life_cycle'] != 5) {
                $updateData = array_merge($updateData, ['life_cycle' => 4]);
            }
            $row->update($updateData);
        }
        UserInfo::updateOrCreate(['user_id' => $id], $request->only(['full_name', 'first_name', 'last_name', 'company', 'extend']));
        return $this->success();
    }
}
