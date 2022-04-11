<?php

namespace Composer\Http;

use Illuminate\Routing\Controller;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;

abstract class BaseController extends Controller
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    public function success($data = null)
    {
        return response()->json($data);
    }

    public function fail($errcode = 0, $errmsg = '')
    {
        return response()->json(['errcode' => $errcode, 'errmsg' => $errmsg]);
    }
}
