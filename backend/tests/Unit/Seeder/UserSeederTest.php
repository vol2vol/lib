<?php

namespace Tests\Unit\Seeder;

use App\Models\Role;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserSeederTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
    }

    public function test_user_seeder_creates_users()
    {
        $this->seed(UserSeeder::class);

        $this->assertDatabaseCount('users', 2);
        $this->assertDatabaseHas('users', ['login' => 'admin', 'role_id' => Role::where('role_name', 'admin')->get()->first()['role_id']]);
        $this->assertDatabaseHas('users', ['login' => 'user', 'role_id' => Role::where('role_name', 'user')->get()->first()['role_id']]);
    }

    public function test_user_seeder_does_not_duplicate_users()
    {
        $this->seed(UserSeeder::class);
        $this->seed(UserSeeder::class);

        $this->assertDatabaseCount('users', 2);
    }
}