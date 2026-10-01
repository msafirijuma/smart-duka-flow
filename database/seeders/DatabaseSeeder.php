<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Roles
        // Role::create(['name' => 'admin']);
        // Role::create(['name' => 'owner']);
        // Role::create(['name' => 'manager']);
        // Role::create(['name' => 'cashier']);

        $this->call([
            RoleSeeder::class,
        ]);
    }
}
