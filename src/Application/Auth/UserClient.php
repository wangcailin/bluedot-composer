<?php

namespace Composer\Application\Auth;

use Composer\Exceptions\ApiErrorCode;
use Composer\Exceptions\ApiException;
use Composer\Support\Auth\Models\User;
use Composer\Http\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserClient extends Controller
{
    public function __construct(User $user)
    {
        $this->model = $user;

        $this->allowedIncludes = ['roles'];

        $this->validateCreateRules = [
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

    public function handleCreate()
    {
        if ($this->model::where('username', $this->data['username'])->first()) {
            throw new ApiException('账号已存在，请重新输入', ApiErrorCode::ACCOUNT_REPEAT_ERROR);
        }
        $this->row = $this->model::createUser($this->data['username'], $this->data['password'], empty($this->data['email']) ?: '', empty($this->data['phone']) ?: '');
        if (isset($this->data['roles'])) {
            $this->row->assignRole($this->data['roles']);
            app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        }
    }

    public function change(Request $request, $id)
    {
        $user = $this->model::findOrFail($id);
        $validateData = $request->validate([
            'is_active' => [Rule::in([0, 1])],
            'roles' => ['array']
        ]);
        if ($validateData) {
            $user->update($validateData);
            if (isset($validateData['roles'])) {
                $user->syncRoles($validateData['roles']);
                app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
            }
        }
        return $this->success($user);
    }

    public function beforeDelete()
    {
        if ($this->id == '1') {
            throw new ApiException('不能删除超级管理员', ApiErrorCode::VALIDATION_ERROR);
        }
    }
}
