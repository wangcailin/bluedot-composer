<?php

namespace Composer\Http\Traits;

use Illuminate\Support\Facades\Auth;

trait PlatformBindUser
{
    public function beforeCreate()
    {
        $this->data = request()->all();
        $this->data['user_id'] = Auth::guard('platform')->id();
        $this->handleCreateValidate();
    }

    public function UserFilter()
    {
        $this->model = $this->model->where('user_id', Auth::guard('platform')->id());
    }
}
