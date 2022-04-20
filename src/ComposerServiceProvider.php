<?php

namespace Composer;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Gate;

class ComposerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->makeLog();
        $this->makePassport();

        $this->publishes([
            __DIR__ . '/../config/composer.php' => config_path('composer.php'),
        ], 'composer-config');

        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'composer-migrations');

        $this->publishes([
            __DIR__ . '/../database/seeders' => database_path('seeders'),
        ], 'composer-seeders');

        Gate::before(function ($user, $ability) {
            if ($user->hasRole('Super-Admin')) {
                return true;
            }
        });
        Composer::routes();
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
