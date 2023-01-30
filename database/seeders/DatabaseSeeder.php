<?php

use Illuminate\Database\Seeder;
use Composer\Support\Auth\Models\User;

class DatabaseSeeder extends Seeder
{

    public function run()
    {
        User::createAdminUser('bluedot', 'Bluedot@2023');
    }
}
