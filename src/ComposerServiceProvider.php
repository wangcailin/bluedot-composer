<?php

namespace BluedotComposer;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

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
            __DIR__ . '/../config/passport.php' => config_path('passport.php'),
        ], 'composer-passport-config');

        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'composer-migrations');

        $this->publishes([
            __DIR__ . '/../database/seeders' => database_path('seeders'),
        ], 'composer-seeders');

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views'),
        ], 'composer-views');


        $this->commands([
            Console\KeyCommand::class,
            Console\InstallCommand::class,
            Console\EnvCommand::class,
        ]);
    }

    public function register()
    {
        $this->registerPaginator();
        $this->registerHandler();
        $this->registerHttpKernel();

        // 合并配置文件
        $this->mergeConfigFrom(
            __DIR__ . '/../config/passport.php',
            'passport'
        );

        // Passport 13.x 使用自动发现，无需手动注册
        // PassportServiceProvider 会自动加载
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
        // 新版 Passport 从配置文件或默认路径加载密钥
        // 如果需要自定义路径，可以通过以下方式设置
        if (config('passport.key_path')) {
            Passport::loadKeysFrom(base_path(config('passport.key_path')));
        }
        // 否则，Passport 会自动从 storage_path('oauth-private.key') 和 storage_path('oauth-public.key') 加载

        // 配置 Token 过期时间
        Passport::tokensExpireIn(now()->addHours(2));
        Passport::refreshTokensExpireIn(now()->addDays(1));
        Passport::personalAccessTokensExpireIn(now()->addDays(7));
    }



    /**
     * 分页器
     *
     * @return void
     */
    protected function registerPaginator()
    {
        $this->app->singleton('Illuminate\Pagination\LengthAwarePaginator', function ($app, $options) {
            return new \BluedotComposer\Support\Database\Models\Paginator($options['items'], $options['total'], $options['perPage'], $options['currentPage'], $options['options']);
        });
    }

    protected function registerHandler()
    {
        $this->app->singleton(
            \Illuminate\Contracts\Debug\ExceptionHandler::class,
            \BluedotComposer\Exceptions\Handler::class
        );
    }

    protected function registerHttpKernel()
    {
        $this->app->singleton(
            \Illuminate\Contracts\Http\Kernel::class,
            \BluedotComposer\Http\Kernel::class
        );
    }
}
