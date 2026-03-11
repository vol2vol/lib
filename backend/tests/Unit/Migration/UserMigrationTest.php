<?php

namespace Tests\Unit;

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
    }

    public function test_users_table_has_primary_key()
    {
        $columns = Schema::getColumns('users');
        $primaryKeyColumn = collect($columns)->firstWhere('name', 'user_id');

        $this->assertTrue($primaryKeyColumn['auto_increment']);
    }
}
