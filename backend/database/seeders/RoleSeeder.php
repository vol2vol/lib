<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::updateOrCreate(
            ['role_name' => 'admin'],
            ['role_name' => 'admin']
        );

        Role::updateOrCreate(
            ['role_name' => 'user'],
            ['role_name' => 'user']
        );
    }
}
