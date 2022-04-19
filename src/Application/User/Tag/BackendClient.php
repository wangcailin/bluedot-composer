<?php

namespace Composer\Application\User\Tag;

use Composer\Application\User\Models\Relation\UserTagCount;
use Composer\Http\Controller;
use Spatie\QueryBuilder\AllowedFilter;

class BackendClient extends Controller
{
    public function __construct(UserTagCount $userTagCount)
    {
        $this->model = $userTagCount;
        $this->allowedFilters = [
            AllowedFilter::exact('id'),
            AllowedFilter::exact('user_id'),
            AllowedFilter::exact('tag_id'),
        ];
    }

    public function performBuildFilter()
    {
        $this->model->with('tag');
    }

    public function getUserList()
    {
        $this->buildFilter();
        $this->performBuildFilterList();
        $pageSize = (int) request()->input('pageSize', 10);
        $this->list = $this->model->paginate($pageSize, ['*'], 'current');
        return $this->success($this->list);
    }
}
