<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         User::firstOrCreate(
            ['login' => 'admin'],
            [
                'password' => 'password',
                'role_id' => 1,
            ]
        );

        User::firstOrCreate(
            ['login' => 'user'],
            [
                'password' => 'password',
                'role_id' => 2,
            ]
        );
    }
}
