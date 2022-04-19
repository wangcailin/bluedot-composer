<?php

namespace Composer\Application\WeChat\Response\Handler;

use Composer\Application\WeChat\Models\Material;
use EasyWeChat\Kernel\Messages\Raw;

class ReplyMaterialHandle
{
    public $id;
    public $openid;
    public $app;
    public function __construct($id, $openid, $app)
    {
        $this->id = $id;
        $this->openid = $openid;
        $this->app = $app;
    }

    public function handle()
    {
        $material = Material::find($this->id);
        switch ($material['type']) {
            case 2:
                $data = [];
                $data['touser'] = $this->openid;
                $data['msgtype'] = 'link';
                $data['link'] = [
                    'title' => empty($material['data']['title']) ? '' : $material['data']['title'],
                    'description' => empty($material['data']['description']) ? '' : $material['data']['description'],
                    'url' => $material['data']['url'],
                    'thumb_url' => $material['data']['image'],
                ];
                $this->app->customer_service->message(new Raw(json_encode($data)))->to($this->openid)->send();
                return;
                break;
            case 3:
                return $material['data']['text'];
                break;
        }
        return;
    }
}
