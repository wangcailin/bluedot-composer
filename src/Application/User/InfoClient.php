<?php

namespace Composer\Application\User;

use Composer\Application\User\Models\UserInfo;
use Composer\Http\Controller;

class InfoClient extends Controller
{
    public function __construct(UserInfo $userInfo)
    {
        $this->model = $userInfo;
    }

    public function get($id)
    {
        $row = $this->model->firstWhere('user_id', $id);
        return $this->success($row);
    }
}
