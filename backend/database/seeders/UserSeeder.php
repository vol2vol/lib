<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Role;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            ['login' => 'admin', 'password' => 'password', 'role_id' => Role::where('role_name', 'admin')->get()->first()['role_id']],
            ['login' => 'user', 'password' => 'password', 'role_id' => Role::where('role_name', 'user')->get()->first()['role_id']]
        ];

        foreach ($users as $user) {
            DB::table('users')->insertOrIgnore($user);
        };
    }
}
