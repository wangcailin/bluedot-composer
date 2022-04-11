<?php

namespace Composer\Application\Event;

use Composer\Application\Event\Models\Chat;
use Composer\Http\Controller;
use Spatie\QueryBuilder\AllowedFilter;

class ChatClient extends Controller
{

    public function __construct(Chat $chat)
    {
        $this->model = $chat;
        $this->allowedFilters = [
            AllowedFilter::exact('event_id'),
            AllowedFilter::exact('state'),
        ];
    }

    use \Composer\Application\Event\Traits\GetList;

    public function getAll($id)
    {
        $list = $this->model->where(['event_id' => $id, 'state' => 1])->orderBy('sort', 'DESC')->get()->transform(function ($item) {
            return $item->append('is_like');
        });
        return $this->success($list);
    }

}
