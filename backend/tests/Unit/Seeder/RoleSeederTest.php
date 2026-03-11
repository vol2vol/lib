<?php

namespace Tests\Unit;

use App\Models\Role;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_role_seeder_creates_admin_and_user_roles()
    {
        $this->seed(RoleSeeder::class);

        $this->assertDatabaseCount('roles', 2);
        $this->assertDatabaseHas('roles', ['role_name' => 'admin']);
        $this->assertDatabaseHas('roles', ['role_name' => 'user']);
    }

    public function test_role_seeder_does_not_duplicate_roles()
    {
        $this->seed(RoleSeeder::class);
        $this->seed(RoleSeeder::class); // Запускаем дважды

        $this->assertDatabaseCount('roles', 2); // Должно остаться 2
    }
}