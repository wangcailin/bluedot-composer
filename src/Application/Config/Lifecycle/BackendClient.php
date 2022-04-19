<?php

namespace Composer\Application\Config\Lifecycle;

use Composer\Application\Config\Lifecycle\Models\Lifecycle;
use Composer\Application\User\Models\User;
use Composer\Http\Controller;

class BackendClient extends Controller
{
    public function __construct(Lifecycle $lifecycle)
    {
        $this->model = $lifecycle;
    }

    public function getList()
    {
        $row = $this->model->firstOrCreate(['data' => ['register' => ['phone', 'email']]]);
        return $this->success($row);
    }

    public function create()
    {
        $data = request()->input('data');
        $row = $this->model->updateOrCreate(['data' => $data]);
        return $this->success($row);
    }

    public function getCount(User $user)
    {
        $data = [
            [
                'name' => '访客',
                'total' => $user->where('life_cycle', 1)->count(),
            ], [
                'name' => '粉丝',
                'total' => $user->where('life_cycle', 2)->count(),
            ], [
                'name' => '标注',
                'total' => $user->where('life_cycle', 3)->count(),
            ], [
                'name' => '注册',
                'total' => $user->where('life_cycle', 4)->count(),
            ], [
                'name' => '现客',
                'total' => $user->where('life_cycle', 5)->count(),
            ],
        ];
        return $this->success($data);
    }
}
