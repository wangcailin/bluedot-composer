<?php

namespace Composer\Console;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;

class EnvCommand extends Command
{
    use ConfirmableTrait;

    protected $signature = 'composer:env';

    protected $description = 'Set the application Env';

    public function handle()
    {
        file_exists('.env') && copy('.env', '.env.dev');
        file_exists('.env') && copy('.env', '.env.production');

        if (!$this->setKeyInEnvironmentFile('APP_ENV', 'dev', 'production')) {
            return;
        }
        if (!$this->setKeyInEnvironmentFile('APP_DEBUG', 'true', 'false')) {
            return;
        }

        $this->info('Application Env set successfully.');
    }

    protected function setKeyInEnvironmentFile($key, $before, $after)
    {
        $content = file_get_contents('.env.production');
        file_put_contents('.env.production', str_replace(
            "{$key}={$before}",
            "{$key}={$after}",
            $content
        ));

        return true;
    }
}
