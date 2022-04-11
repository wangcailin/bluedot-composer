<?php

namespace Composer\Application\Event\Traits;

trait GetOne
{
    public function get($id)
    {
        $this->row = $this->model->firstWhere('event_id', $id);
        return $this->success($this->row);
    }
}
