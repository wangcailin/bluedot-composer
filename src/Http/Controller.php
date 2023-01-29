<?php

namespace Composer\Http;

use Spatie\QueryBuilder\QueryBuilder;
use Composer\Http\Traits\Select;
use Composer\Http\Traits\Validate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class Controller extends BaseController
{
    use Select;
    use Validate;

    /** 模型对象 */
    public Model $model;

    public array $allowedFilters = ['id'];
    public string $defaultSorts = '-id';
    public array $allowedSorts = [];
    public array $allowedIncludes = [];
    public array $allowedAppends = [];

    public string|null $guard = null;

    /**
     * 数据是否绑定当前管理员ID
     */
    public bool $authUserId = true;

    /** 列表数据 */
    public array $list = [];

    /** 详情数据 */
    public array $row = [];

    /** id */
    public string|int|null $id = null;

    /** 创建数据 */
    public array $data = [];

    /**
     * 获取list
     */
    public function getList()
    {
        $this->beforeBuildFilter();
        $this->buildFilter();
        $this->afterBuildFilter();

        $pageSize = (int) request()->input('pageSize', 10);
        $this->list = $this->model->paginate($pageSize, ['*'], 'current');
        $this->afterList();

        return $this->success($this->list);
    }

    /**
     * 获取list
     */
    public function getAllList()
    {
        $this->beforeBuildFilter();
        $this->buildFilter();
        $this->afterBuildFilter();

        $this->list = $this->model->get();
        $this->afterList();

        return $this->success($this->list);
    }

    /**
     * 获取row
     */
    public function get(string $id)
    {
        $this->id = $id;

        $this->beforeGet();
        $this->row = $this->model->findOrFail($id);
        $this->afterGet();

        return $this->success($this->row);
    }

    /**
     * 创建
     */
    public function create()
    {
        $this->data = request()->all();
        if ($this->authUserId) {
            $this->createAuthUserId();
        }

        $this->handleCreateValidate();

        $this->beforeCreate();
        $this->row = $this->model->create($this->data);
        $this->afterCreate();

        return $this->success($this->row);
    }

    public function update(string $id)
    {
        $this->id = $id;
        $this->data = request()->all();

        $this->handleUpdateValidate();

        $this->beforeUpdate();
        $this->row = $this->model::findOrFail($id);
        $this->row->update($this->data);
        $this->afterUpdate();

        return $this->success($this->row);
    }

    public function delete(string $id)
    {
        $this->id = $id;

        $this->beforeDelete();
        $this->model::findOrFail($id)->delete();
        $this->afterDelete();

        return $this->success();
    }

    public function beforeBuildFilter(): void
    {
    }
    public function buildFilter(): void
    {
        $this->model = QueryBuilder::for($this->model)
            ->defaultSorts($this->defaultSorts)
            ->allowedFilters($this->allowedFilters)
            ->allowedSorts($this->allowedSorts)
            ->allowedIncludes($this->allowedIncludes)
            ->allowedAppends($this->allowedAppends);
    }
    public function afterBuildFilter(): void
    {
    }
    public function afterList(): void
    {
    }

    public function beforeCreate(): void
    {
    }
    public function afterCreate(): void
    {
    }

    public function beforeGet(): void
    {
    }
    public function afterGet(): void
    {
    }

    public function beforeUpdate(): void
    {
    }
    public function afterUpdate(): void
    {
    }

    public function beforeDelete(): void
    {
    }
    public function afterDelete(): void
    {
    }

    public function sort()
    {
        $ids = request()->input('ids', []);
        foreach ($ids as $key => $value) {
            $this->model->where('id', $value)->update(['sort' => $key]);
        }
        return $this->success();
    }

    public function createAuthUserId(): void
    {
        if ($this->guard) {
            $authUserId = Auth::guard($this->guard)->id();
        } else {
            $authUserId = Auth::id();
        }
        $this->data['auth_user_id'] = $authUserId;
    }
}
