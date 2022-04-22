<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Laravel\Passport\ClientRepository;
use Composer\Support\Auth\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::createAdminUser('bluedot', 'Bluedot@2022');

        $client = new ClientRepository();
        $client->createPasswordGrantClient(null, 'Default Tenant Client', '');
        $client->createPersonalAccessClient(null, 'Default Tenant Personal Client', '');
    }
}
