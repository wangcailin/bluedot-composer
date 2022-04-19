<?php

namespace Composer\Application\Event;

use Composer\Application\Event\Models\Event;
use Composer\Application\Event\Models\Relation\EventTag;
use Composer\Http\Controller;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;

class EventClient extends Controller
{
    use \Composer\Application\Event\Live\Client;

    public function __construct(Event $event)
    {
        $this->model = $event;
        $this->allowedFilters = [
            AllowedFilter::exact('id'),
            'title',
            'tag.name',
            AllowedFilter::exact('event_type'),
            AllowedFilter::exact('is_show'),
            AllowedFilter::scope('time_state'),
        ];
        $this->allowedSorts = ['start_time', 'end_time'];
    }

    public function performGet()
    {
        $this->model->withCount('register');
    }

    public function performUpdate()
    {
        $tagIds = request()->input('tag_ids');
        if ($tagIds) {
            EventTag::where('event_id', $this->row['id'])->whereNotIn('tag_id', $tagIds)->delete();
            foreach ($tagIds as $key => $value) {
                EventTag::firstOrCreate(['event_id' => $this->row['id'], 'tag_id' => $value]);
            }
        }
    }

    public function userEvent(Request $request)
    {
        $timeState = $request->input('time_state');
        $userId = $request->input('user_id');
        $list = $this->model->whereIn('id', function ($query) use ($userId) {
            $query->select('event_id')
                ->from('event_register')
                ->whereRaw('event_register.event_id = event.id')
                ->where('user_id', $userId);
        })->where(function ($query) use ($timeState) {
            $time = date('Y-m-d H:i:s');
            if ($timeState == 'notstarted') {
                $query->where('start_time', '>', $time);
            } elseif ($timeState == 'underway') {
                $query->where('start_time', '<', $time);
                $query->where('end_time', '>', $time);
            } elseif ($timeState == 'finished') {
                $query->where('end_time', '<', $time);
            }
            return $query;
        })->get();
        return $this->success($list);
    }
}
