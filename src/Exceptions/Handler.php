<?php

namespace Composer\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{

    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof \Spatie\Permission\Exceptions\UnauthorizedException) {
            $errcode = 403;
            $errmsg = 'You do not have required authorization.';
        } elseif ($exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
            $errcode = ApiErrorCode::MODEL_NOT_FOUND_ERROR;
            $errmsg = 'Resource does not exist.';
        } elseif ($exception instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
            $errcode = $exception->getStatusCode();
            $errmsg = $exception->getMessage();
        } elseif ($exception instanceof \Illuminate\Validation\ValidationException) {
            $errcode = ApiErrorCode::VALIDATION_ERROR;
            $errmsg = $exception->errors();
        } elseif ($exception instanceof \Composer\Exceptions\ApiException) {
            $errcode = $exception->getCode();
            $errmsg = $exception->getMessage();
        } else {
            $errcode = 500;
            $errmsg = 'server error.';
        }
        return $this->response($errmsg, $errcode);
    }

    protected function response($errmsg, $errcode)
    {
        return response()->json([
            'errmsg' => $errmsg,
            'errcode'  => $errcode,
        ], 200);
    }
}
