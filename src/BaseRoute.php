<?php

namespace Composer;

use Composer\Route\BackendRoute;
use Illuminate\Support\Facades\Route;

class BaseRoute
{

    /**
     * Binds the Passport routes into the controller.
     *
     * @param  callable|null  $callback
     * @param  array  $options
     * @return void
     */
    public static function routes($callback = null, array $options = [])
    {
        $callback = $callback ?: function ($router) {
            $router->all();
        };

        $defaultOptions = [
            'prefix' => 'api',
            //'namespace' => '\Composer\Application\Auth',
        ];

        $options = array_merge($defaultOptions, $options);
        Route::group($options, function ($router) use ($callback) {
            $callback(new BackendRoute($router));
        });
    }
}
