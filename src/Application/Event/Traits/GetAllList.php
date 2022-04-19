<?php

namespace Composer\Application\Event\Traits;

trait GetAllList
{
    public function getAllList()
    {
        $eventID = request()->input('event_id');
        $list = $this->model->where('event_id', $eventID)->orderBy('sort', 'DESC')->get();
        return $this->success($list);
    }
}
