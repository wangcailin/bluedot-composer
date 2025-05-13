<?php

namespace BluedotComposer\Application\WeChat;

use BluedotComposer\Application\WeChat\Models\Reply;
use BluedotComposer\Http\Controller;
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
