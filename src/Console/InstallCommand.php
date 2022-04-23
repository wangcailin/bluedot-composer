<?php

namespace Composer\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'composer:install';

    protected $description = 'Run the commands necessary to prepare Bluedot Composer for use';

    public function handle()
    {
        $this->call('vendor:publish', ['--tag' => 'composer-config']);
        $this->call('vendor:publish', ['--tag' => 'composer-migrations']);
        $this->call('vendor:publish', ['--tag' => 'composer-seeders']);

        $this->call('composer:key');
    }
}
