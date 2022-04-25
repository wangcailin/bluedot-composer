<?php

namespace Composer\Application\Auth;

use Composer\Exceptions\ApiErrorCode;
use Composer\Exceptions\ApiException;
use Composer\Support\Auth\Models\User;
use Composer\Http\Controller;

class UserClient extends Controller
{
    public function __construct(User $user)
    {
        $this->model = $user;

        $this->allowedIncludes = ['roles'];

        $this->validateRules = [
            'username' => 'required',
            'password' => 'required',
        ];
    }

    public function get($id)
    {
        $user = $this->model::findOrFail($id);
        $user['roles'] = $user->roles->pluck('name');
        return $this->success($user);
    }

    public function create()
    {
        $this->performCreate();
        if ($this->model::where('username', $this->data['username'])->first()) {
            throw new ApiException('账号已存在，请重新输入', ApiErrorCode::ACCOUNT_REPEAT_ERROR);
        }
        $user = $this->model::createUser($this->data['username'], $this->data['password'], empty($this->data['email']) ?: '', empty($this->data['phone']) ?: '');
        if (!empty($this->data['roles'])) {
            $user->assignRole($this->data['roles']);
        }
        return $this->success($user);
    }

    public function change($id)
    {
        $user = $this->model::findOrFail($id);
        $data = request()->only(['is_active', 'roles']);
        $user->update($data);
        $roles = $user->roles->pluck('name');
        foreach ($roles as $key => $value) {
            $user->removeRole($value);
        }
        $user->assignRole($data['roles']);
        return $this->success($user);
    }

    public function performDelete()
    {
        if ($this->id == '1') {
            throw new ApiException('不能删除超级管理员', ApiErrorCode::VALIDATION_ERROR);
        }
    }
}
