<?php

namespace Composer\Application\Auth;

use Composer\Http\BaseController;
use Spatie\Permission\Models\Permission;

class PermissionClient extends BaseController
{

    public function getSelect(Permission $permission)
    {
        $list = $permission->select('name as value', 'name as label')->get();
        return $this->success($list);
    }
}
