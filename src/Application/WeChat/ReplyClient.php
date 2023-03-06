<?php

namespace Composer\Application\WeChat;

use Composer\Application\WeChat\Models\Reply;
use Composer\Http\Controller;
use Spatie\QueryBuilder\AllowedFilter;

class ReplyClient extends Controller
{
    public function __construct(Reply $reply)
    {
        $this->model = $reply;
        $this->allowedFilters = [
            AllowedFilter::exact('appid'),
            AllowedFilter::exact('type'),
            'text',
        ];
    }

    public function afterBuildFilter()
    {
        $this->model->with('authorizer');
    }
}
