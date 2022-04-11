<?php

namespace Composer\Application\Event;

use Composer\Application\Event\Models\Register\Config;
use Composer\Http\Controller;

class RegisterConfigClient extends Controller
{
    public function __construct(Config $config)
    {
        $this->model = $config;
    }

    use \Composer\Application\Event\Traits\GetOne;
    use \Composer\Application\Event\Traits\Create;

}
