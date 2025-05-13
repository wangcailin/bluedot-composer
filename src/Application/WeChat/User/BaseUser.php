<?php

namespace BluedotComposer\Application\WeChat\User;

use BluedotComposer\Application\WeChat\Models\WeChatOpenid;

abstract class BaseUser
{
    protected $where;
    protected $data;
    protected $user;

    protected function asyncWeChatOpenid()
    {
        $this->user = WeChatOpenid::updateOrCreate($this->where, $this->data);
    }

    public function getUser()
    {
        return $this->user;
    }
}
