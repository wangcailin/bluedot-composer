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

        $this->info('Application Env set successfully.');
    }

    protected function setKeyInEnvironmentFile($key, $before, $after)
    {
        $this->writeNewEnvironmentFileWith($key, $before, $after);

        return true;
    }

    protected function writeNewEnvironmentFileWith($key, $before, $after)
    {
        $environmentFilePath = $this->laravel->environmentFilePath();
        $before = file_get_contents($environmentFilePath);
        file_put_contents($environmentFilePath, str_replace(
            "{$key}={$before}",
            "{$key}=\"{$after}\"",
            $before
        ));
    }
}
