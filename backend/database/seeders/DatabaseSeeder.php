<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            DemoSeeder::class
        ]);
        // System roles come from config. Create the owner with app:create-admin.
    }
}
