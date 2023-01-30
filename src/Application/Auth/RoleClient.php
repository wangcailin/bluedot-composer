<?php

namespace Composer\Application\Auth;

use Composer\Http\Controller;
use Illuminate\Validation\Rule;

class RoleClient extends Controller
{
    public function __construct()
    {
        $this->model = config('permission.models.role');

        $this->allowedIncludes = ['permissions'];

        $this->validateRules = [
            'name' => ['required', Rule::unique(config('permission.models.role'))],
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

    public function handleUpdate()
    {
        $this->row = $this->model::findOrFail($this->id);
        $data = request()->all();
        $this->row->update(['name' => $data['name']]);
        $permission = $this->row->permissions->pluck('name');
        foreach ($permission as $key => $value) {
            $this->row->revokePermissionTo($value);
        }
        $this->row->givePermissionTo($data['permission']);
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function getValidateUpdateRules()
    {
        $this->validateRules['name'] = ['required', Rule::unique(config('permission.models.role'))->ignore($this->id),];
        return $this->validateRules;
    }

    public function handleCreate()
    {
        $this->row = $this->model::create(['name' => $this->data['name']]);
        if (isset($this->data['permission'])) {
            $this->row->givePermissionTo($this->data['permission']);
            app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        }
    }

    public function getSelect()
    {
        $this->buildFilter();
        $list = $this->model->select('id as value', 'name as label')->get();
        return $this->success($list);
    }
}
