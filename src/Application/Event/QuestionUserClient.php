<?php

namespace Composer\Application\Event;

use Composer\Application\Event\Models\Question\User;
use Composer\Http\Controller;
use Spatie\QueryBuilder\AllowedFilter;

class QuestionUserClient extends Controller
{
    use \Composer\Application\Event\Traits\GetOneUser;

    public function __construct(User $user)
    {
        $this->model = $user;
        $this->allowedFilters = [
            AllowedFilter::exact('event_id'),
        ];
    }
}
