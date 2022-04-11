<?php

namespace Composer\Application\Config\System;

use Composer\Application\Config\System\Models\System;
use Composer\Http\Controller;

class Client extends Controller
{
    public function __construct(System $system)
    {
        $this->model = $system;
    }

    public function getList()
    {
        $row = $this->model->firstWhere([
            'type' => request()->input('type'),
        ]);
        return $this->success($row);
    }

    public function create()
    {
        $data = request()->input('data');
        $type = request()->input('type');

        $row = $this->model->updateOrCreate([
            'type' => $type,
        ], ['data' => $data]);
        return $this->success($row);
    }
}
