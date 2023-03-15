<?php

namespace Composer\Http;

use Illuminate\Routing\Controller;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;


/**
 * Http基类控制器
 */
abstract class BaseController extends Controller
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;


    /**
     * 统一正确返回
     *
     * @param [type] $data
     * @return void
     */
    public function success($data = null)
    {
        return response()->json($data);
    }

    /**
     * 统一错误返回
     *
     * @param integer $errcode 错误码
     * @param string $errmsg 错误信息
     * @return void
     */
    public function fail($errcode = 0, $errmsg = '')
    {
        return response()->json(['errcode' => $errcode, 'errmsg' => $errmsg]);
    }
}
