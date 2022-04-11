<?php

namespace Composer\Exceptions;

use Composer\Http\Response;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof \Spatie\Permission\Exceptions\UnauthorizedException) {
            return response()->json([
                'errmsg' => 'You do not have required authorization.',
                'errcode'  => 403,
            ], 403);
        }

        return parent::render($request, $exception);

        if (Str::contains($request->server('REQUEST_URI'), '/api/')) {
            $api = new Response();

            if ($exception instanceof AuthenticationException) {
                $response = $api->errorUnauthorized();
            } else if ($exception instanceof ValidationException) {
                $response = $api->errorBadRequest($exception->validator->errors()->first());
            } else if ($exception instanceof ModelNotFoundException) {
                $response = $api->errorNotFound();
            } else if ($exception instanceof NotFoundHttpException) {
                $response = $api->errorNotFound();
            } else if ($exception instanceof MethodNotAllowedHttpException) {
                $response = $api->errorMethodNotAllowed();
            } else {
                $response = $api->fail($exception->getMessage());
            }
            return $response;
        } else {
            return parent::render($request, $exception);
        }
    }
}
