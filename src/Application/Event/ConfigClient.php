<?php

namespace Composer\Application\Event;

use Composer\Application\Event\Models\Config;
use Composer\Http\Controller;
use Spatie\QueryBuilder\AllowedFilter;

class ConfigClient extends Controller
{
    use \Composer\Application\Event\Traits\GetOne;
    use \Composer\Application\Event\Traits\Create;

    public function __construct(Config $config)
    {
        $this->model = $config;
        $this->allowedFilters = [
            AllowedFilter::exact('event_id'),
        ];
    }
}
