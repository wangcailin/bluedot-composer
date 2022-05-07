<?php

namespace Composer\Application\WeChat\User;

use Composer\Application\WeChat\Models\WeChatOpenid;

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
