<?php

namespace Composer\Application\Push;

use Composer\Application\Push\Models\Push;
use Composer\Http\Controller;

class Client extends Controller
{

    public function __construct(Push $push)
    {

        $this->model = $push;
        $this->allowedFilters = [];
    }
}
