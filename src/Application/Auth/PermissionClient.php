<?php

namespace Composer\Application\Auth;

use Composer\Http\BaseController;
use Spatie\Permission\Models\Permission;

class PermissionClient extends BaseController
{
    public function __construct(Permission $permission)
    {
        $this->model = $permission;
    }

    public function getSelect()
    {
        $list = $this->model->select('name as value', 'name as label')->get();
        return $this->success($list);
    }
}
