<?php

namespace BluedotComposer\Application\Auth;

use BluedotComposer\Support\Auth\Client;
use BluedotComposer\Support\Auth\Models\User;

class BackendClient extends Client
{
    public function __construct(User $user)
    {
        $this->model = $user;
    }
}
