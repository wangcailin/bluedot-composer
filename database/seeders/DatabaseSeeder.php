<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Composer\Application\Auth\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $superAdminRole = Role::create(['name' => 'Super-Admin']);
        $user = User::createAdminUser('bluedot', 'Bluedot@2022');
        $user->assignRole($superAdminRole);
    }
}
