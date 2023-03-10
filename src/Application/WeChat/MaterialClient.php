<?php

namespace Composer\Application\WeChat;

use Composer\Http\Controller;
use Spatie\QueryBuilder\AllowedFilter;
use Composer\Application\WeChat\WeChat;
use Composer\Application\WeChat\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class MaterialClient extends Controller
{
    public function __construct(Material $material)
    {
        $this->model = $material;
        $this->allowedFilters = [
            AllowedFilter::exact('type'),
            'name',
        ];
    }

    public function getWeChatList(Request $request, WeChat $weChat)
    {
        $input = $request->validate([
            'appid' => 'required',
            'type' => Rule::in('image', 'video', 'voice', 'article'),
            'current' => 'required',
            'pageSize' => 'required',
        ]);
        $api = $weChat->getOfficialAccount($input['appid'])->getClient();

        $offset = ($input['current'] - 1) * $input['pageSize'];
        $options = [
            'offset' => $offset,
            'count' => $input['pageSize'],
        ];
        if ($input['type'] == 'article') {
            $options['no_content'] = 1;
            $response = $api->postJson('/cgi-bin/freepublish/batchget', $options)->toArray();
        } else {
            $options['type'] = $input['type'];
            $response = $api->postJson('/cgi-bin/material/batchget_material', $options)->toArray();
        }

        $list = [
            'total' =>  $response['total_count'],
            'data' =>  $response['item'],
            'pageSize' => $input['pageSize'],
            'current' => $input['current'],
        ];

        return $this->success($list);
    }

    public function uploadWeChat(Request $request, WeChat $weChat)
    {
        $input = $request->validate([
            'appid' => 'required',
            'type' => Rule::in('image', 'video', 'voice'),
            'file' =>  'file',
        ]);

        $api = $weChat->getOfficialAccount($input['appid'])->getClient();

        $fileName = $input['file']->storeAs('', $input['file']->getClientOriginalName(), 'uploads');
        $filePath = Storage::disk('uploads')->path($fileName);

        $result = [];
        if ($input['type'] == 'image') {
            $result = $api->withFile($filePath, 'media')->post('/cgi-bin/material/add_material?type=' . $input['type']);
        }
        Storage::disk('uploads')->delete($fileName);

        return $this->success($result);
    }

    public function getWeChat($mediaId, Request $request, WeChat $weChat)
    {
        $input = $request->validate([
            'appid' => 'required',
        ]);
        $api = $weChat->getOfficialAccount($input['appid'])->getClient();
        $response = $api->postJson('/cgi-bin/material/get_material', ['media_id' => $mediaId]);
        return $this->success($response->toArray());
    }
}
