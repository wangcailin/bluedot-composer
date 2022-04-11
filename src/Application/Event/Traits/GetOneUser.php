<?php

namespace Composer\Application\Event\Traits;

trait GetOneUser
{
    public function get($id)
    {
        $userId = request()->input('user_id');
        $this->row = $this->model->firstWhere(['event_id' => $id, 'user_id' => $userId]);
        return $this->success($this->row);
    }
}
