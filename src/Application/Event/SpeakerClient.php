<?php

namespace Composer\Application\Event;

use Composer\Application\Event\Models\Speaker;
use Composer\Http\Controller;
use Spatie\QueryBuilder\AllowedFilter;

class SpeakerClient extends Controller
{
    

    public function __construct(Speaker $speaker)
    {
        $this->model = $speaker;
        $this->allowedFilters = [AllowedFilter::exact('event_id')];
    }

    use \Composer\Application\Event\Traits\GetAllList;
    use \Composer\Application\Event\Traits\GetMany;

    public function create()
    {
        $eventID = request()->input('event_id');
        $speakerList = request()->input('speaker_list');
        $ids = [];
        foreach ($speakerList as $key => $value) {
            $value['sort'] = $key;
            if (empty($value['id'])) {
                $value['event_id'] = $eventID;
                $ids[] = $this->model->create($value)->id;
            } else {
                $ids[] = $value['id'];
                $this->model->find($value['id'])->update($value);
            }
        }
        $this->model->whereNotIn('id', $ids)->where('event_id', $eventID)->delete();
        $list = $this->model->where('event_id', $eventID)->orderBy('sort', 'desc')->get();
        return $this->success($list);
    }

}
