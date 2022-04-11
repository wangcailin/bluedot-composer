<?php
namespace Composer\Application\Analysis;

use Composer\Application\Analysis\Models\Monitor;
use Composer\Application\User\Models\Relation\UserTag;
use Composer\Application\User\Models\User;
use Composer\Http\Controller;

class MonitorClient extends Controller
{
    public function __construct(Monitor $monitor)
    {
        $this->model = $monitor;
    }

    public function create()
    {
        $this->performCreate();
        if (!empty($this->data['page_param'])) {
            $this->data['page_param'] = json_decode($this->data['page_param']);
        }

        if (!empty($this->data['keywords'])) {
            $this->data['keywords'] = json_decode($this->data['keywords']);
            $userId = $this->data['user_id'];
            $user = User::find($userId);
            if ($user['life_cycle'] < 3) {
                $user->life_cycle = 3;
                $user->save();
            }
            $sourceType = 0;
            if (isset($this->data['source_type']) && $this->data['source_type']) {
                $sourceType = $this->data['source_type'];
            }
            foreach ($this->data['keywords'] as $key => $value) {
                UserTag::create(['user_id' => $userId, 'tag_id' => $value, 'source_type' => $sourceType, 'page_event_type' => $this->data['page_event_type']]);
            }
        }
        $this->row = $this->model::create($this->data);
        return $this->success($this->row);
    }
}
