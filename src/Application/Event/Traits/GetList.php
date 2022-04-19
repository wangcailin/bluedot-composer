<?php

namespace Composer\Application\Event\Traits;

trait GetList
{
    public function getList()
    {
        $this->buildFilter();
        $this->performBuildFilterList();
        $pageSize = (int) request()->input('pageSize', 10);
        $this->list = $this->model->paginate($pageSize, ['*'], 'current');
        return $this->success($this->list);
    }
}
