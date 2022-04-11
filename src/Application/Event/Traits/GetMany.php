<?php

namespace Composer\Application\Event\Traits;

trait GetMany
{
    public function get($id)
    {
        $this->list = $this->model->where('event_id', $id)->orderBy('sort', 'ASC')->get();
        return $this->success($this->list);
    }
}
