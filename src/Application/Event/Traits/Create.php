<?php

namespace Composer\Application\Event\Traits;

trait Create
{
    public function create()
    {
        $this->performCreate();
        $list = $this->model->updateOrCreate(['event_id' => $this->data['event_id']], $this->data);
        return $this->success($list);
    }
}
