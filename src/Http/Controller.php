<?php

namespace Composer\Http;

use Spatie\QueryBuilder\QueryBuilder;
use Composer\Http\Traits\Select;
use Composer\Http\Traits\Validate;
use Illuminate\Support\Facades\Auth;

/**
 * 统一Http控制器
 */
class Controller extends BaseController
{
    use Select;
    use Validate;

    /**
     * 当前模型
     *
     * @var [type]
     */
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
     * 获取列表
     *
     * @return void
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
     * 获取所有数据
     *
     * @return void
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
     * 获取单行数据
     *
     * @param [type] $id
     * @return void
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
     * 创建数据
     *
     * @return void
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

    /**
     * 更新数据
     *
     * @param [type] $id
     * @return void
     */
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

    /**
     * 删除数据
     *
     * @param [type] $id
     * @return void
     */
    public function delete($id)
    {
        $this->id = $id;

        $this->beforeDelete();
        $this->handleDelete();
        $this->afterDelete();

        return $this->success();
    }

    /**
     * BuildFilter 前置方法
     *
     * @return void
     */
    public function beforeBuildFilter()
    {
    }

    /**
     * BuildFilter 方法
     *
     * @return void
     */
    public function buildFilter()
    {
        $this->model = QueryBuilder::for($this->model)
            ->defaultSorts($this->defaultSorts)
            ->allowedFilters($this->allowedFilters)
            ->allowedSorts($this->allowedSorts)
            ->allowedIncludes($this->allowedIncludes);
    }
    /**
     * BuildFilter 后置方法
     *
     * @return void
     */
    public function afterBuildFilter()
    {
    }

    /**
     * 获取列表 核心方法
     *
     * @return void
     */
    public function handleList()
    {
        $pageSize = (int) request()->input('pageSize', 10);
        $this->list = $this->model->paginate($pageSize, ['*'], 'current');
    }
    /**
     * 获取列表 后置方法
     *
     * @return void
     */
    public function afterList()
    {
    }

    /**
     * 创建数据 前置方法
     *
     * @return void
     */
    public function beforeCreate()
    {
    }

    /**
     * 创建数据 核心方法
     *
     * @return void
     */
    public function handleCreate()
    {
        $this->row = $this->model::create($this->data);
    }

    /**
     * 创建数据 后置方法
     *
     * @return void
     */
    public function afterCreate()
    {
    }

    /**
     * 获取单个数据 前置方法
     *
     * @return void
     */
    public function beforeGet()
    {
    }
    /**
     * 获取单个数据 核心方法
     *
     * @return void
     */
    public function handleGet()
    {
        $this->row = $this->model->findOrFail($this->id);
    }
    /**
     * 获取单个数据 后置方法
     *
     * @return void
     */
    public function afterGet()
    {
    }

    /**
     * 更新数据 前置方法
     *
     * @return void
     */
    public function beforeUpdate()
    {
    }

    /**
     * 更新数据 核心方法
     *
     * @return void
     */
    public function handleUpdate()
    {
        $this->row = $this->model::findOrFail($this->id);
        $this->row->update($this->data);
    }
    /**
     * 更新数据 后置方法
     *
     * @return void
     */
    public function afterUpdate()
    {
    }

    /**
     * 删除数据 前置方法
     *
     * @return void
     */
    public function beforeDelete()
    {
    }
    /**
     * 删除数据 核心方法
     *
     * @return void
     */
    public function handleDelete()
    {
        $this->model::findOrFail($this->id)->delete();
    }
    /**
     * 删除数据 后置方法
     *
     * @return void
     */
    public function afterDelete()
    {
    }

    /**
     * 拖动排序
     *
     * @return void
     */
    public function sort()
    {
        $ids = request()->input('ids', []);
        foreach ($ids as $key => $value) {
            $this->model->where('id', $value)->update(['sort' => $key]);
        }
        return $this->success();
    }

    /**
     * 创建数据 注入后台用户ID auth_user_id
     *
     * @return void
     */
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
