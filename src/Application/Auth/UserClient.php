<?php

namespace Composer\Application\Auth;

use Composer\Support\Auth\Models\User;
use Composer\Http\Controller;

class UserClient extends Controller
{
    public function __construct(User $user)
    {
        $this->model = $user;
    }

    public function get($id)
    {
        $user = User::find($id);
        $user['role'] = $user->roles->pluck('name');
        return $this->success($user);
    }

    public function create()
    {
        $this->performCreate();
        if (User::where('username', $this->data['username'])->first()) {
            return $this->success(['errcode' => 1, 'errmsg' => '账号已存在，请重新输入']);
        }
        $user = User::createUser($this->data['username'], $this->data['password'], empty($this->data['email']) ?: '', empty($this->data['phone']) ?: '');
        if (!empty($this->data['role'])) {
            $user->assignRole($this->data['role']);
        }
        return $this->success($user);
    }

    public function change($id)
    {
        $user = $this->model::findOrFail($id);
        $data = request()->all();
        $user->update($data);
        $auth = User::find($id);
        $roles = $auth->roles->pluck('name');
        foreach ($roles as $key => $value) {
            $auth->removeRole($value);
        }
        $auth->assignRole($data['role']);
        $this->performUpdate();
        return $this->success($user);
    }
}
