<?php

namespace Composer\Application\Push\Commands;

use Composer\Application\Push\Models\Push;
use Composer\Application\User\Models\Relation\UserGroup;
use Composer\Application\User\Models\User;
use Composer\Application\User\Models\WeChatUser;
use Composer\Application\WeChat\Models\Material;
use Composer\Application\WeChat\WeChat;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PushService extends Command
{
    /**
     * 命令名称及签名
     *
     * @var string
     */
    protected $signature = 'push:service';

    /**
     * 命令描述
     *
     * @var string
     */
    protected $description = '推送服务';

    /**
     * 创建命令
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 执行命令
     *
     * @param  \App\Support\DripEmailer  $drip
     * @return mixed
     */
    public function handle()
    {
        Log::info(
            'SendSchedule start --> '
        );

        $time = date('Y-m-d H:i:00');
        $result = Push::where([
            'state' => 0,
            'push_time' => $time,
        ])->get();

        Log::info(
            'SendSchedule start --> get ' . $time . ' --> ' . count($result)
        );

        Push::where([
            'state' => 0,
            'push_time' => $time,
        ])->update(['state' => 1]);

        foreach ($result as $key => $value) {
            switch ($value['type']) {
                case 1:
                    switch ($value['data']['type']) {
                        case 1: // 图文
                            $material = Material::find($value['data']['id']);

                            if ($value['data']['group_id']) {
                                $groupUser = UserGroup::where('group_id', $value['data']['group_id'])->get();
                                Log::info($groupUser);
                                $weChat = new WeChat();
                                $app = $weChat->getOfficialAccount($value['data']['appid']);
                                if (count($groupUser) > 0) {
                                    $tag = $app->user_tag->create(uniqid());
                                    Log::info($tag);
                                    $value->update(['data->tag' => $tag['tag']]);
                                    // $tag

                                    foreach ($groupUser as $k => $v) {
                                        $user = User::find($v['user_id']);
                                        $wechatUser = WeChatUser::firstWhere(['appid' => $value['data']['appid'], 'unionid' => $user['unionid']]);
                                        $app->user_tag->tagUsers([$wechatUser['openid']], $tag['tag']['id']);
                                    }

                                    $msg = $app->broadcasting->sendNews($material['name'], $tag['tag']['id']);
                                    $value->update([
                                        'data->msg' => [
                                            'id' => $msg['msg_id'],
                                            'data_id' => $msg['msg_data_id'],
                                        ]]);
                                }
                            }

                    }
            }
        }
    }
}
