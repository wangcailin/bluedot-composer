<?php

namespace Composer\Application\Event;

use Composer\Application\Event\Models\Schedule;
use Composer\Http\Controller;
use Spatie\QueryBuilder\AllowedFilter;

class ScheduleClient extends Controller
{

    public function __construct(Schedule $schedule)
    {
        $this->model = $schedule;
        $this->allowedFilters = [AllowedFilter::exact('event_id')];
    }

    use \Composer\Application\Event\Traits\GetMany;

    public function create()
    {
        $request = request();
        $eventID = $request->input('event_id');
        $scheduleList = $request->input('schedule_list');
        $ids = [];
        foreach ($scheduleList as $key => $value) {
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
