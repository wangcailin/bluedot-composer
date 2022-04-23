<?php

namespace Composer\Application\Auth;

use Composer\Support\Auth\Client;
use Composer\Support\Auth\Models\User;

class BackendClient extends Client
{
    public function __construct(User $user)
    {
        $this->model = $user;
    }
}
