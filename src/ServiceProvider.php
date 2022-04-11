<?php

namespace Composer;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider as AbstractServiceProvider;
use Laravel\Passport\Passport;

class ServiceProvider extends AbstractServiceProvider
{
    public function boot()
    {
        $this->makeLog();
        $this->makePassport();

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    public function register()
    {
        $this->registerPaginator();
        $this->registerHandler();
        $this->registerHttpKernel();

        $this->app->register(
            \Laravel\Passport\PassportServiceProvider::class
        );
        $this->app->register(\Spatie\Permission\PermissionServiceProvider::class);
    }

    protected function makeLog()
    {
        DB::listen(function ($query) {
            $data = [
                'sql' => $query->sql,
                'bindings' => $query->bindings,
                'time' => $query->time,
            ];
            Log::info(json_encode($data));
        });
    }

    protected function makePassport()
    {
        Passport::loadKeysFrom(base_path(config('passport.key_path')));
        Passport::tokensExpireIn(now()->addHour(2));
        Passport::refreshTokensExpireIn(now()->addDays(1));
        Passport::personalAccessTokensExpireIn(now()->addDays(7));
    }



    /**
     * 分页器
     */
    protected function registerPaginator()
    {
        $this->app->singleton('Illuminate\Pagination\LengthAwarePaginator', function ($app, $options) {
            return new \Composer\Support\Paginator($options['items'], $options['total'], $options['perPage'], $options['currentPage'], $options['options']);
        });
    }

    protected function registerHandler()
    {
        $this->app->singleton(
            \Illuminate\Contracts\Debug\ExceptionHandler::class,
            \Composer\Exceptions\Handler::class
        );
    }

    protected function registerHttpKernel()
    {
        $this->app->singleton(
            \Illuminate\Contracts\Http\Kernel::class,
            \Composer\Http\Kernel::class
        );
    }
}
