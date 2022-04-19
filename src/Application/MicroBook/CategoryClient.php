<?php

namespace Composer\Application\MicroBook;

use Composer\Application\MicroBook\Models\MicroBookCategory;
use Composer\Http\Controller;
use Spatie\QueryBuilder\AllowedFilter;

class CategoryClient extends Controller
{
    public function __construct(MicroBookCategory $microBookCategory)
    {
        $this->model = $microBookCategory;
        $this->allowedFilters = ['name'];
        $this->defaultSort = 'sort';
    }
}
