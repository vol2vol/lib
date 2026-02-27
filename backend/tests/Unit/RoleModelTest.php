<?php

namespace Tests\Unit;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_role_has_correct_fillable_fields()
    {
        $role = Role::factory()->make();

        $this->assertEquals(['role_name'], $role->getFillable());
    }

    public function test_role_has_custom_primary_key()
    {
        $role = Role::factory()->create();

        $this->assertEquals('role_id', $role->getKeyName());
        $this->assertTrue($role->exists);
    }

    public function test_role_has_timestamps()
    {
        $role = Role::factory()->create();

        $this->assertNotNull($role->created_at);
        $this->assertNotNull($role->updated_at);
    }

    public function test_role_can_be_created_with_valid_data()
    {
        $role = Role::factory()->create([
            'role_name' => 'admin',
        ]);

        $this->assertDatabaseHas('roles', [
            'role_name' => 'admin',
        ]);
    }

    public function test_role_has_many_users()
    {
        $role = Role::factory()->create();
        $users = User::factory()->count(3)->create([
            'role_id' => $role->role_id,
        ]);

        $this->assertCount(3, $role->users);
        $this->assertInstanceOf(User::class, $role->users->first());
    }

    public function test_role_users_relationship_uses_correct_foreign_key()
    {
        $role = Role::factory()->create();
        $user = User::factory()->create([
            'role_id' => $role->role_id,
        ]);

        $this->assertTrue($role->users->contains($user));
        $this->assertEquals($role->role_id, $user->role_id);
    }

    public function test_role_can_have_no_users()
    {
        $role = Role::factory()->create();

        $this->assertCount(0, $role->users);
    }

    public function test_role_name_is_required()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        Role::create([]);
    }

    public function test_role_name_must_be_unique()
    {
        Role::factory()->create(['role_name' => 'admin']);

        $this->expectException(\Illuminate\Database\QueryException::class);
        Role::factory()->create(['role_name' => 'admin']);
    }

    public function test_can_create_admin_role()
    {
        $adminRole = Role::factory()->create(['role_name' => 'admin']);

        $this->assertEquals('admin', $adminRole->role_name);
    }

    public function test_can_create_user_role()
    {
        $userRole = Role::factory()->create(['role_name' => 'user']);

        $this->assertEquals('user', $userRole->role_name);
    }
}