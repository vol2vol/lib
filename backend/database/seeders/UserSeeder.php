<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'user_id' => 1,
                'login' => 'admin',
                'password' => Hash::make('password'),
                'role_id' => 1,
            ],
            [
                'user_id' => 2,
                'login' => 'user',
                'password' => Hash::make('password'),
                'role_id' => 2,
            ],
        ]);
    }
}
