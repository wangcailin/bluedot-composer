<?php

namespace Composer\Application\System;

use Composer\Http\Controller;
use Composer\Application\System\Models\Config;
use Illuminate\Http\Request;

class ConfigClient extends Controller
{
    public function __construct(Config $config)
    {
        $this->model = $config;
    }

    public function get($type)
    {
        $this->beforeGet();
        $this->row = $this->model::firstWhere('type', $type);
        return $this->success($this->row);
    }

    public function updateOrCreate(Request $request)
    {
        $input = $request->all();

        if ($input['type'] == 'mail') {
            $this->checkMailAliyun($input['data']);
        }

        $this->row = $this->model->updateOrCreate(['type' => $request['type']], $input);
        return $this->success($this->row);
    }

    private function checkMailAliyun($config)
    {
    }
}
