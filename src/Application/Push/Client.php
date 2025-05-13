<?php

namespace BluedotComposer\Application\Push;

use BluedotComposer\Application\Push\Models\Push;
use BluedotComposer\Http\Controller;

class Client extends Controller
{
    public function __construct(Push $push)
    {
        $this->model = $push;
        $this->allowedFilters = [];
    }
}
