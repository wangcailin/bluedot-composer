<?php

namespace Composer\Application\Auth;

use Composer\Http\Controller;
use Composer\Support\Auth\Models\Role as RoleModel;
use Spatie\Permission\Models\Role;

class RoleClient extends Controller
{
    public function __construct(RoleModel $roleModel)
    {
        $this->model = $roleModel;
    }

    public function get($id)
    {
        $role = Role::findById($id);
        $role['permission'] = $role->permissions->pluck('name');
        return $this->success($role);
    }

    public function update($id)
    {
        $role = Role::findOrFail($id);
        $data = request()->all();
        $role->update(['name' => $data['name']]);
        $permission = $role->permissions->pluck('name');
        foreach ($permission as $key => $value) {
            $role->revokePermissionTo($value);
        }
        $role->givePermissionTo($data['permission']);
        return $this->success($role);
    }

    public function create()
    {
        $this->performCreate();
        $role = Role::create(['name' => $this->data['name']]);
        $role->givePermissionTo($this->data['permission']);
        return $this->success($role);
    }

    public function getSelect()
    {
        $list = $this->model->select('name as value', 'name as label')->get();
        return $this->success($list);
    }
}
