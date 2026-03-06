<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRoleAssignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_is_created_with_default_role()
    {
        $user = User::factory()->create();

        $this->assertNotNull($user->role_id);
        $this->assertDatabaseHas('users', [
            'user_id' => $user->user_id,
            'role_id' => $user->role_id,
        ]);
        $this->assertEquals('user', $user->role->role_name);
    }

    public function test_user_can_be_assigned_admin_role()
    {
        $adminRole = Role::factory()->create(['role_name' => 'admin']);
        $user = User::factory()->create(['role_id' => $adminRole->role_id]);

        $this->assertEquals('admin', $user->role->role_name);
    }

    public function test_user_can_be_assigned_regular_role()
    {
        $userRole = Role::factory()->create(['role_name' => 'user']);
        $user = User::factory()->create(['role_id' => $userRole->role_id]);

        $this->assertEquals('user', $user->role->role_name);
    }
}