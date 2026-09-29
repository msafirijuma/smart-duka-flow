<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Roles
        Role::create(['name' => 'Super Admin']);
        Role::create(['name' => 'HR']);
        Role::create(['name' => 'Manager']);
        Role::create(['name' => 'Employee']);

        $this->call([
            
        ]);
    }
}
