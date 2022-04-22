<?php

namespace Composer\Application\Auth;

use Composer\Http\Controller;

class RoleClient extends Controller
{
    public function __construct()
    {
        $this->model = config('permission.models.role');

        $this->allowedIncludes = ['permissions'];

        $this->validateRules = [
            'name' => 'required|unique:' . config('permission.models.role'),
        ];

        $this->validateMessage = [
            'unique' => '请输入唯一的部门名',
        ];
    }

    public function get($id)
    {
        $role = $this->model::findById($id);
        $role['permission'] = $role->permissions->pluck('name');
        return $this->success($role);
    }

    public function update($id)
    {
        $role = $this->model::findOrFail($id);
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
        $role = $this->model::create(['name' => $this->data['name']]);
        $role->givePermissionTo($this->data['permission']);
        return $this->success($role);
    }

    public function getSelect()
    {
        $this->buildFilter();
        $list = $this->model->select('name as value', 'name as label')->get();
        return $this->success($list);
    }
}
