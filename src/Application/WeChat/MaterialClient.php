<?php

namespace Composer\Application\WeChat;

use Composer\Application\WeChat\Models\Authorizer;
use Composer\Application\WeChat\Models\Material;
use Composer\Http\Controller;
use Spatie\QueryBuilder\AllowedFilter;

class MaterialClient extends Controller
{
    public $weChat;

    public function __construct(WeChat $weChat, Material $material)
    {
        $this->weChat = $weChat;
        $this->model = $material;
        $this->allowedFilters = [
            AllowedFilter::exact('type'),
            'name',
        ];
    }

    public function getAsync(Authorizer $authorizer)
    {
        $type = request()->input('filter.type', 1);
        $authorizerData = $authorizer::where([
            'type' => 1,
        ])->get();

        foreach ($authorizerData as $key => $value) {
            $start = 0;
            $limit = 10;
            $count = 0;

            $app = $this->weChat->getOfficialAccount($value['appid']);

            do {
                $list = $app->material->list('news', $start, $limit);
                $count = $list['total_count'];
                foreach ($list['item'] as $k => $v) {
                    $time = date('Y-m-d H:i:s', $v['update_time']);
                    $data = [
                        'name' => $v['media_id'],
                        'appid' => $value['appid'],
                        'type' => $type,
                        'data' => $v['content'],
                        'created_at' => $time,
                        'updated_at' => $time,
                    ];
                    $this->model->updateOrCreate(['name' => $v['media_id']], $data);
                }
                $start += $limit;
            } while ($count > ($start + $limit));
        }

        return $this->success();
    }
}
