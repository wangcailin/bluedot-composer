<?php

namespace Composer\Application\Event\Live;

use Composer\Application\Event\Models\Live\PPT as PPTModels;
use Composer\Support\Vhall;
use Illuminate\Http\Request;

trait Client
{
    /**
     * 生成直播间
     */
    public function createRoom($id)
    {
        $result = Vhall::createRoom();
        $this->model->where('id', $id)->update([
            'live_room_id' => $result['data']['room_id'],
            'live_create_room_time' => date('Y-m-d H:i:s'),
        ]);
        return $this->success();
    }

    /**
     * 创建回放
     */
    public function createRoomVod($id)
    {
        $row = $this->model->findOrFail($id);
        $result = Vhall::createRoomVod($row['live_room_id'], $row['start_time'], $row['end_time']);
        $this->model->where('id', $id)->update([
            'live_playback' => 1,
            'live_vod_id' => $result['data']['vod_id'],
            'live_task_id' => $result['data']['task_id'],
        ]);
        return $this->success();

    }

    /**
     * 获取token
     */
    public function getRoomToken(Request $request)
    {
        $liveRoomId = $request->input('live_room_id');
        $thirdPartyUserId = $request->input('third_party_user_id');
        $result = Vhall::getAccessToekn($liveRoomId, $thirdPartyUserId);
        return $this->success($result['data']);
    }

    public function getRoomCurrentTime($id)
    {
        $event = $this->model->find($id);
        $time = time();
        $startTime = strtotime($event['start_time']);
        $code = 0;
        if ($time >= $startTime && $time <= strtotime($event['end_time'])) {
            $code = 1;
        }
        $currentTime = $time - $startTime;
        return $this->success(['code' => $code, 'current_time' => $currentTime]);
    }

    /**
     * 获取直播开始时间
     */
    public function getCountDown($id)
    {
        $event = $this->model->find($id);
        $time = time();
        $startTime = strtotime($event['start_time']);
        $seconds = $startTime - $time;
        return $this->success(['seconds' => $seconds]);
    }

    public function uploadPPT(Request $request)
    {
        $file = $request->file('file');
        $eventId = $request->input('event_id');
        $filename = time() . '.pdf';
        $path = storage_path() . '/upload/';
        $file->move($path, $filename);
        dispatch(new Job($path . $filename, $eventId));
        return $this->success();
    }

    public function getPPT($id)
    {
        $row = PPTModels::firstOrCreate(['event_id' => $id]);
        return $this->success($row);
    }
}
