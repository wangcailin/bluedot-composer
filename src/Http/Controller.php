<?php

namespace Composer\Http;

use Spatie\QueryBuilder\QueryBuilder;
use Composer\Http\Traits\Select;
use Composer\Http\Traits\Validate;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    use Select;
    use Validate;

    /** 模型对象 */
    protected $model;

    public $allowedFilters = ['id'];
    public $defaultSorts = '-id';
    public $allowedSorts = [];
    public $allowedIncludes = [];

    public $guard = null;

    /**
     * 数据是否绑定当前管理员ID
     */
    public $authUserId = true;

    /** 列表数据 */
    public $list = [];

    /** 详情数据 */
    public $row = [];

    /** id */
    public $id = [];

    /** 创建数据 */
    public $data = [];

    /**
     * 获取list
     */
    public function getList()
    {
        $this->beforeBuildFilter();
        $this->buildFilter();
        $this->afterBuildFilter();

        $this->handleList();
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
    public function get($id)
    {
        $this->id = $id;

        if ($this->authUserId) {
            $this->createAuthUserId();
        }

        $this->beforeGet();
        $this->handleGet();
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
        $this->handleCreate();
        $this->afterCreate();

        return $this->success($this->row);
    }

    public function update($id)
    {
        $this->id = $id;
        $this->data = request()->all();

        $this->handleUpdateValidate();

        $this->beforeUpdate();
        $this->handleUpdate();
        $this->afterUpdate();

        return $this->success($this->row);
    }

    public function delete($id)
    {
        $this->id = $id;

        $this->beforeDelete();
        $this->handleDelete();
        $this->afterDelete();

        return $this->success();
    }

    public function beforeBuildFilter()
    {
    }
    public function buildFilter()
    {
        $this->model = QueryBuilder::for($this->model)
            ->defaultSorts($this->defaultSorts)
            ->allowedFilters($this->allowedFilters)
            ->allowedSorts($this->allowedSorts)
            ->allowedIncludes($this->allowedIncludes);
    }
    public function afterBuildFilter()
    {
    }
    public function handleList()
    {
        $pageSize = (int) request()->input('pageSize', 10);
        $this->list = $this->model->paginate($pageSize, ['*'], 'current');
    }
    public function afterList()
    {
    }

    public function beforeCreate()
    {
    }
    public function handleCreate()
    {
        $this->row = $this->model::create($this->data);
    }
    public function afterCreate()
    {
    }

    public function beforeGet()
    {
    }
    public function handleGet()
    {
        $this->row = $this->model->findOrFail($this->id);
    }
    public function afterGet()
    {
    }

    public function beforeUpdate()
    {
    }
    public function handleUpdate()
    {
        $this->row = $this->model::findOrFail($this->id);
        $this->row->update($this->data);
    }
    public function afterUpdate()
    {
    }

    public function beforeDelete()
    {
    }
    public function handleDelete()
    {
        $this->model::findOrFail($this->id)->delete();
    }
    public function afterDelete()
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

    public function createAuthUserId()
    {
        if ($this->guard) {
            $authUserId = Auth::guard($this->guard)->id();
        } else {
            $authUserId = Auth::id();
        }
        $this->data['auth_user_id'] = $authUserId;
    }
}
