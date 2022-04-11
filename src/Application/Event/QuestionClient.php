<?php

namespace Composer\Application\Event;

use Composer\Application\Event\Models\Question\Question;
use Composer\Http\Controller;
use Spatie\QueryBuilder\AllowedFilter;

class QuestionClient extends Controller
{

    public function __construct(Question $question)
    {
        $this->model = $question;
        $this->allowedFilters = [AllowedFilter::exact('event_id')];
    }

    use \Composer\Application\Event\Traits\GetOne;
    use \Composer\Application\Event\Traits\Create;

    
}
