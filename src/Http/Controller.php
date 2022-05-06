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
    public $defaultSort = '-id';
    public $allowedSorts = [];
    public $allowedIncludes = [];
    public $allowedAppends = [];

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
     */
    public function getList()
    {
        $this->buildFilter();
        $this->performBuildFilterList();
        $pageSize = (int) request()->input('pageSize', 10);
        $this->list = $this->model->paginate($pageSize, ['*'], 'current');
        $this->afterList();
        return $this->success($this->list);
    }

    /**
     * 获取全部
     */
    public function getAllList()
    {
        $this->buildFilter();
        $this->list = $this->model->get();
        $this->afterList();
        return $this->success($this->list);
    }

    public function get($id)
    {
        $this->id = $id;
        $this->performGet();
        $this->row = $this->model->findOrFail($id);
        $this->afterGet();
        return $this->success($this->row);
    }

    public function create()
    {
        $this->performCreate();
        $this->row = $this->model::create($this->data);
        $this->afterCreate();
        return $this->success($this->row);
    }

    public function update($id)
    {
        $this->id = $id;
        $this->performUpdate();
        $this->row = $this->model::findOrFail($id);
        $this->row->update($this->data);
        $this->afterUpdate();
        return $this->success($this->row);
    }

    public function delete($id)
    {
        $this->id = $id;
        $this->performDelete();
        $this->model::findOrFail($id)->delete();
        $this->afterDelete();
        return $this->success();
    }

    public function performCreate()
    {
        $this->data = request()->all();
        if ($this->authUserId) {
            $this->createAuthUserId();
        }
        $this->handleCreateValidate();
    }
    public function afterCreate()
    {
    }

    public function performGet()
    {
    }
    public function afterGet()
    {
    }

    public function performUpdate()
    {
        $this->data = request()->all();
        $this->handleUpdateValidate();
    }
    public function afterUpdate()
    {
    }

    public function afterList()
    {
    }

    public function performDelete()
    {
    }
    public function afterDelete()
    {
    }

    public function buildFilter()
    {
        $this->model = QueryBuilder::for($this->model)
            ->defaultSort($this->defaultSort)
            ->allowedFilters($this->allowedFilters)
            ->allowedSorts($this->allowedSorts)
            ->allowedIncludes($this->allowedIncludes)
            ->allowedAppends($this->allowedAppends);

        $this->performBuildFilter();
    }

    public function performBuildFilter()
    {
    }

    public function performBuildFilterList()
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
