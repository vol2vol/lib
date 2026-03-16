<?php

namespace Tests\Unit\Migration;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class UserMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_table_has_expected_columns()
    {
        $this->assertTrue(Schema::hasTable('users'));
        $this->assertTrue(Schema::hasColumn('users', 'user_id'));
        $this->assertTrue(Schema::hasColumn('users', 'login'));
        $this->assertTrue(Schema::hasColumn('users', 'password'));
        $this->assertTrue(Schema::hasColumn('users', 'role_id'));
        $this->assertTrue(Schema::hasColumn('users', 'created_at'));
        $this->assertTrue(Schema::hasColumn('users', 'updated_at'));
    }

    public function test_users_table_has_primary_key()
    {
        $columns = Schema::getColumns('users');
        $primaryKeyColumn = collect($columns)->firstWhere('name', 'user_id');

        $this->assertTrue($primaryKeyColumn['auto_increment']);
    }

    public function test_user_id_cannot_be_null()
    {
        $columns = Schema::getColumns('users');
        $publisherNameColumn = collect($columns)->firstWhere('name', 'user_id');

        $this->assertFalse($publisherNameColumn['nullable']);
    }

    public function test_role_id_cannot_be_null()
    {
        $columns = Schema::getColumns('users');
        $publisherNameColumn = collect($columns)->firstWhere('name', 'role_id');

        $this->assertFalse($publisherNameColumn['nullable']);
    }

    public function test_login_cannot_be_null()
    {
        $columns = Schema::getColumns('users');
        $publisherNameColumn = collect($columns)->firstWhere('name', 'login');

        $this->assertFalse($publisherNameColumn['nullable']);
    }

    public function test_password_cannot_be_null()
    {
        $columns = Schema::getColumns('users');
        $publisherNameColumn = collect($columns)->firstWhere('name', 'password');

        $this->assertFalse($publisherNameColumn['nullable']);
    }
}
