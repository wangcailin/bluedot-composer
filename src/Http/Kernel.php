<?php

namespace Composer\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'api' => [
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \Composer\Http\Middleware\RequestLog::class
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth.admin' => Middleware\AuthenticateAdmin::class,
        'auth.operation_log' => Middleware\AuthOperationLog::class,
        'auth.role' => \Spatie\Permission\Middlewares\RoleMiddleware::class,
        'auth.permission' => \Spatie\Permission\Middlewares\PermissionMiddleware::class,
        'auth.role_or_permission' => \Spatie\Permission\Middlewares\RoleOrPermissionMiddleware::class,
        'auth.client_credentials' => \Laravel\Passport\Http\Middleware\CheckClientCredentials::class,
    ];
}
